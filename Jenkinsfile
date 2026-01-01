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
                sh 'composer install --no-interaction --prefer-dist'
            }
        }

        stage('Run Tests') {
            steps {
                sh 'php artisan test'
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


