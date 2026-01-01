pipeline {
    agent any

    options {
        timestamps()
        timeout(time: 20, unit: 'MINUTES')
    }

    environment {
        APP_ENV = 'testing'
        APP_DEBUG = 'false'
    }

    stages {

        stage('Checkout Code') {
            steps {
                checkout scm
            }
        }

        stage('Verify Tools') {
            steps {
                bat 'php -v'
                bat 'composer -V'
            }
        }

        stage('Install Dependencies') {
            steps {
                bat '''
                    if not exist vendor (
                        composer install --no-interaction --prefer-dist
                    ) else (
                        echo Dependencies already installed
                    )
                '''
            }
        }

        stage('Prepare Environment') {
            steps {
                bat '''
                    if not exist .env (
                        copy .env.example .env
                    )
                    php artisan key:generate
                '''
            }
        }

        stage('Clear & Cache Config') {
            steps {
                bat '''
                    php artisan config:clear
                    php artisan cache:clear
                '''
            }
        }

        stage('Run Tests') {
            steps {
                bat 'php artisan test'
            }
        }
    }

    post {
        success {
            echo '✅ CI PASSED: Laravel API build successful'
        }
        failure {
            echo '❌ CI FAILED: Check logs above'
        }
        always {
            cleanWs()
        }
    }
}
