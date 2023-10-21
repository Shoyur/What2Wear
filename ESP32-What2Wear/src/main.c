#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include "esp_http_client.h"

void app_main() {
    // Configure the URL and HTTP client
    esp_http_client_config_t config = {
        .url = "http://s974927839.online-home.ca/What2Wear/server/tempController.php",
        .event_handler = ESP_OK,
    };
    
    esp_http_client_handle_t client = esp_http_client_init(&config);

    esp_http_client_set_method(client, HTTP_METHOD_POST);

    srand(time(0));
    int temp = rand() % 40;
    char temp_str[50];
    sprintf(temp_str, "%d", temp);
    char *post_data = strcat("temp=", temp_str);

    esp_http_client_set_post_field(client, post_data, strlen(post_data));

    esp_err_t err = esp_http_client_perform(client);
    
    esp_http_client_cleanup(client);
    
    if (err == ESP_OK) {
        printf("HTTP POST request successful\n");
    } else {
        printf("HTTP POST request failed\n");
    }
}