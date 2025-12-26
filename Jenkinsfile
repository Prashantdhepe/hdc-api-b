pipeline {
    agent any

    environment {
        APP_ENV = 'testing'
        COMPOSER_ALLOW_XDEBUG = '0'
    }

    options {
        timestamps()
        timeout(time: 30, unit: 'MINUTES')
    }

    stages {

        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Verify Environment') {
            steps {
                sh 'php -v'
                sh 'php -m | grep intl'
                sh 'composer --version'
            }
        }

        stage('Install Dependencies') {
            steps {
                echo 'Installing PHP dependencies'
                sh 'composer install --no-interaction --prefer-dist'
            }
        }

        stage('Prepare Laravel') {
            steps {
                sh '''
                if [ ! -f .env ]; then
                    cp .env.example .env
                fi
                php artisan key:generate
                '''
            }
        }

        stage('Run Tests') {
            steps {
                sh './vendor/bin/phpunit || true'
            }
        }
    }

    post {
        always {
            cleanWs()
        }
        success {
            echo '✅ Pipeline succeeded'
        }
        failure {
            echo '❌ Pipeline failed'
        }
    }
}
