container_php: str = 'php_bundle_1'
container_db: str = 'mysql_bundle_1'

waiting_db_connection: bool = True
phpunit_code_error_bypass: bool = False

containers: list = [
    container_php,
    container_db
]

container_work_dir: str = '/www'

docker_compose_files_list: list[str] = [
    'docker-compose.yaml'
]

commands: dict[str, str] = {
    'composer install': 'composer install',
    'composer run phpunit': 'composer run phpunit',
    'composer run phpstan': 'composer run phpstan',
    'composer run psalm': 'composer run psalm',
    'composer run phpmd': 'composer run phpmd',
}
