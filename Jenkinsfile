// pipeline {
//     agent any

//     environment {
//         APP_ENV = 'hdc-backend'
//         DEPLOY_DIR = '/var/www/hdc/BackendNew'
//     }

//     stages {

//         stage('Checkout Code') {
//             steps {
//                 git branch: 'main',
//                     url: 'https://github.com/prashantdhepe94/hdc-api.git'
//             }
//         }

//         stage('Install Dependencies') {
//             steps {
//                 sh 'composer install --no-interaction --prefer-dist --optimize-autoloader'
//             }
//         }

//         stage('Environment Setup (CI Only)') {
//             steps {
//                 sh '''
//                     if [ ! -f .env ]; then
//                         cp .env.example .env
//                         php artisan key:generate
//                     fi
//                 '''
//             }
//         }

//         stage('Config Cache Check') {
//             steps {
//                 sh '''
//                     php artisan config:clear
//                     php artisan config:cache
//                 '''
//             }
//         }

//         stage('Basic Health Check') {
//             steps {
//                 sh 'php artisan --version'
//             }
//         }

//         stage('Deploy to Server') {
//             steps {
//                 sh '''
//                     rsync -av --delete \
//                     --exclude=.env \
//                     --exclude=storage \
//                     --exclude=vendor \
//                     ./ $DEPLOY_DIR/
//                 '''
//             }
//         }
//     }

//     post {
//         failure {
//             echo '❌ API CI/CD failed'
//         }
//         success {
//             echo '✅ API CI/CD passed and deployed successfully'
//         }
//     }
// }//this used when there is no docker

pipeline {
    agent any

    environment {
        APP_NAME = "hdc-backend"
        DEPLOY_PATH = "/var/www/hdc"
    }

    stages {

        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Build Backend Image') {
            steps {
                sh '''
                  docker build -t hdc-backend:latest .
                '''
            }
        }

    }

    post {
        success {
            echo "✅ Backend image built successfully"
        }
        failure {
            echo "❌ Backend build failed"
        }
    }
}


