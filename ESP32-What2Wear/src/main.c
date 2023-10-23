#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include "esp_http_client.h"

#include "freertos/FreeRTOS.h" // for delay,mutexs,semphrs rtos operations
#include "esp_system.h" // esp_init funtions esp_err_t 
#include "esp_wifi.h" // esp_wifi_init functions and wifi operations
#include "esp_log.h" // for showing logs
#include "esp_event.h" // for wifi event
#include "nvs_flash.h" // non volatile storage
#include "lwip/err.h" // light weight ip packets error handling
#include "lwip/sys.h" // system applications for light weight ip apps

#include "driver/adc.h"


const char *ssid = "Hors_Service_2";
const char *pass = "motdepasse";
int retry_num = 0;

static void wifi_event_handler(void *event_handler_arg, esp_event_base_t event_base, int32_t event_id,void *event_data) {
    
    if (event_id == WIFI_EVENT_STA_START) { 
        printf("WIFI CONNECTING....\n");
        vTaskDelay(500 / portTICK_PERIOD_MS);
    }
    else if (event_id == WIFI_EVENT_STA_CONNECTED) { 
        printf("WiFi CONNECTED\n"); 
    }
    else if (event_id == WIFI_EVENT_STA_DISCONNECTED) { 
        printf("WiFi lost connection\n");
        if (retry_num < 5) { 
            esp_wifi_connect();
            retry_num++;
            printf("Retrying to Connect...\n");
        }
    }
    else if (event_id == IP_EVENT_STA_GOT_IP) {
        printf("Wifi got IP...\n\n");
    }
}

void wifi_connection(){
    esp_netif_init(); // network interdace initialization
    esp_event_loop_create_default(); // responsible for handling and dispatching events
    esp_netif_create_default_wifi_sta(); // sets up necessary data structs for wifi station interface
    wifi_init_config_t wifi_initiation = WIFI_INIT_CONFIG_DEFAULT(); // sets up wifi wifi_init_config struct with default values
    esp_wifi_init(&wifi_initiation); // wifi initialised with dafault wifi_initiation
    esp_event_handler_register(WIFI_EVENT, ESP_EVENT_ANY_ID, wifi_event_handler, NULL); // creating event handler register for wifi
    esp_event_handler_register(IP_EVENT, IP_EVENT_STA_GOT_IP, wifi_event_handler, NULL); // creating event handler register for ip event
    wifi_config_t wifi_configuration ={ // struct wifi_config_t var wifi_configuration
    .sta= {
        .ssid = "",
        .password= "", /*we are sending a const char of ssid and password which we will strcpy in following line so leaving it blank*/ 
    } //also this part is used if you dont want to use Kconfig.projbuild
    };
    strcpy((char*)wifi_configuration.sta.ssid, ssid); // copy chars from hardcoded configs to struct
    strcpy((char*)wifi_configuration.sta.password, pass);
    esp_wifi_set_config(ESP_IF_WIFI_STA, &wifi_configuration); // setting up configs when event ESP_IF_WIFI_STA
    esp_wifi_start(); // start connection with configurations provided in funtion
    esp_wifi_set_mode(WIFI_MODE_STA); // station mode selected
    esp_wifi_connect(); // connect with saved ssid and pass
    printf( "wifi_init_softap finished. SSID: %s password: %s\n", ssid, pass);
}

void app_main() {


    nvs_flash_init(); // this is important in wifi case to store configurations , code will not work if this is not added
    wifi_connection();

    vTaskDelay(1000 / portTICK_PERIOD_MS);

    // Configure the URL and HTTP client
    esp_http_client_config_t config = {
        .url = "http://s974927839.online-home.ca/What2Wear/server/tempController.php",
        .event_handler = ESP_OK,
    };

    esp_http_client_handle_t client = esp_http_client_init(&config);

    esp_http_client_set_method(client, HTTP_METHOD_POST);

    adc1_config_width(ADC_WIDTH_BIT_12);
    adc1_config_channel_atten(ADC1_CHANNEL_0, ADC_ATTEN_DB_11);
    unsigned short adc_value_ch0 = 0;
    float temp = 0;
    
    while (1) {

        adc_value_ch0 = adc1_get_raw(ADC1_CHANNEL_0);
        // printf("\nadc_value_ch0 = %d ", adc_value_ch0);
        temp = (adc_value_ch0 * 3.3) / 4095.0; // ? volts / 3.3
        // printf(" (adc_value_ch0 * 3.3) / 4095.0 = ?volts/3.3 = %f ", temp);
        temp = temp / 0.01; // ? Kelvins
        // printf(" ?Kelvins = %f ", temp);
        temp -= 273.15; // to C
        // printf(" -273.15 = %f°C\n\n", temp);
        
        // Convert the float to a string
        char temp_str[50];
        snprintf(temp_str, sizeof(temp_str), "%.1f", temp);
        // Create the post_data string
        char post_data[55];
        snprintf(post_data, sizeof(post_data), "temp=%s", temp_str);

        esp_http_client_set_post_field(client, post_data, strlen(post_data));
        esp_http_client_perform(client);
        esp_http_client_cleanup(client);

        vTaskDelay(10 * 60 * configTICK_RATE_HZ / 1000);
    }
}