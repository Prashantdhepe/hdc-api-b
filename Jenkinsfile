pipeline {
    agent any

    options {
        timestamps()
    }

    stages {

        stage('Checkout Code') {
            steps {
                checkout scm
            }
        }

        stage('Install Dependencies') {
            steps {
                bat 'composer install --no-interaction --prefer-dist'
            }
        }

        stage('Prepare Environment') {
            steps {
                bat '''
                if not exist .env (
                    copy .env.example .env
                )
                php artisan key:generate --force
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
            echo '✅ CI passed for Laravel API'
        }
        failure {
            echo '❌ CI failed for Laravel API'
        }
    }
}
