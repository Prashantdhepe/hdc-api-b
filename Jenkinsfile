pipeline {
    agent any

    stages {
        stage('Install Dependencies') {
            steps {
                bat 'composer install --no-interaction'
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
