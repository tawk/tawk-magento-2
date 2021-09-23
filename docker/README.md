# Magento 2

## Information
- Magento Versions: 2.3.0, 2.3.1, 2.3.2, 2.3.3, 2.3.4, 2.3.5, 2.4
- PHP Version: 7
- MySQL Version: 5.7

## Pre-Requisites
- install docker-compose [http://docs.docker.com/compose/install/](http://docs.docker.com/compose/install/)
- to run elasticsearch, make sure you have a higher `vm.max_map_count` value. (ex. `vm.max_map_count=262144`)

## Usage
Start the container:

- ```docker-compose --env-file envs/<env-file> up```

Stop the container:

- ```docker-compose --env-file envs/<env-file> stop```

Destroy the container and start from scratch:

- ```docker-compose --env-file envs/<env-file> down```
- ```docker volume rm magento-<volume-name>```
    - ex. ```docker volume rm magento-latest_elasticsearch_data magento-latest_mariadb_data magento-latest_magento_data```

## Setting up the testing environment
### Requirements
- The container should be accessed under `http://127.0.0.1/index.php`  with the admin page under `http://127.0.0.1/index.php/admin`

### Credentials
1. The username and password for the admin is
	- Username: admin
	- password: admin123

### To setup the Tawk widget
1. To run commands in the bash terminal. Run the following:
	- docker ps -al (take note of the container ID of the magento docker container)
	- docker exec -it <CONTAINER ID> bash
2. Enable `production` mode by running:
	- On Versions 2.3.5, 2.4.0
		`/bitnami/magento/htdocs/bin/magento deploy:mode:set production`
	- On versions 2.3.4, 2.3.3, 2.3.2, 2.3.1, 2.3.0
		`/opt/bitnami/magento/htdocs/bin/magento deploy:mode:set production`
3. Follow the knowledge base article to install the tawk application via the manual installation that can be found here https://help.tawk.to/article/magento

## FAQs
1. Did the elasticsearch container failed to run?
	- It might be due to the `vm.max_map_count`. You need to set it to a higher value. Ex. `vm.max_map_count=262144`. You can read it more in [here](https://www.elastic.co/guide/en/elasticsearch/reference/current/vm-max-map-count.html).
	- Command: `sudo sysctl -w vm.max_map_count=262144`
2. Seeing the `An error has happened during application run. See exception log for details.` page after installing the tawk.to module?
	- Just re-run these commands and it should be good to go.
```bin/magento setup:di:compile && bin/magento setup:static-content:deploy && bin/magento cache:clean```

3. Couldn't access the tawk.to module's admin page? Instead, you're being redirected back to the dashboard page with the error `Invalid Form Key. Please refresh the page.`?
	- You can do one of the solutions from this [article](https://www.simicart.com/blog/magento-2-invalid-form-key).

4. Using composer to install the module and got an error of `Fatal error: Allowed memory size of (X number) bytes exhausted.`?
	- You can increase the `memory_limit` before running composer. Ex. `php -d memory_limit=4G <your_composer_command>`
