from typing import Final, Optional

container_php: Final[str] = 'php_bundle_1'
container_db: Optional[str] = 'mysql_bundle_1'

ping_db: Optional[str] = 'mysqladmin ping -h localhost --silent'

waiting_db_connection: Final[bool] = True
phpunit_code_error_bypass: Final[bool] = False

containers: Final[list] = [
    container_php,
    container_db
]

container_work_dir: Final[str] = '/www'

docker_compose_files_list: Final[list[str]] = [
    'docker-compose.yaml',
]

commands: Final[dict[str, str]] = {
    'composer install': 'composer install',
    'composer run phpunit': 'composer run phpunit',
    'composer run phpstan': 'composer run phpstan',
    'composer run psalm': 'composer run psalm',
    'composer run phpmd': 'composer run phpmd',
}
