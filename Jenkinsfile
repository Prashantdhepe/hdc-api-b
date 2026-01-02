//1. when docker is not used its only trigger the jenkins after code commit 

// pipeline {
//     agent any

//     options {
//         timestamps()
//     }

//     stages {

//         stage('Checkout Code') {
//             steps {
//                 checkout scm
//             }
//         }

//         stage('Install Dependencies') {
//             steps {
//                 bat 'composer install --no-interaction --prefer-dist'
//             }
//         }

//         stage('Prepare Environment') {
//             steps {
//                 bat '''
//                 if not exist .env (
//                     copy .env.example .env
//                 )
//                 php artisan key:generate --force
//                 '''
//             }
//         }

//         stage('Run Tests') {
//             steps {
//                 bat 'php artisan test'
//             }
//         }
//     }

//     post {
//         success {
//             echo '✅ CI passed for Laravel API'
//         }
//         failure {
//             echo '❌ CI failed for Laravel API'
//         }
//     }
// }


//2. when docker is used to build the image after code commit


// pipeline {
//     agent any

//     options {
//         timestamps()
//     }

//     environment {
//         IMAGE_NAME = "hdc-api"
//     }

//     stages {

//         stage('Checkout Code') {
//             steps {
//                 checkout scm
//             }
//         }

//         stage('Build Docker Image') {
//             steps {
//                 script {
//                     bat """
//                         docker build -t ${IMAGE_NAME}:${BUILD_NUMBER} .
//                         docker tag ${IMAGE_NAME}:${BUILD_NUMBER} ${IMAGE_NAME}:latest
//                     """
//                 }
//             }
//         }

//     }

//     post {
//         success {
//             echo "✅ Docker image built successfully: ${IMAGE_NAME}:${BUILD_NUMBER}"
//         }
//         failure {
//             echo "❌ Docker image build failed"
//         }
//     }
// }

//2  by this push the docker image to the docker hub after code commit

pipeline {
    agent any

    options {
        timestamps()
    }

    environment {
        IMAGE_NAME = "prashantdev/hdc-api"
        DOCKER_CREDS = credentials('dockerhub_creds')
    }

    stages {

        stage('Checkout Code') {
            steps {
                checkout scm
            }
        }

        stage('Build Docker Image') {
            steps {
                bat """
                    docker build -t ${IMAGE_NAME}:${BUILD_NUMBER} .
                    docker tag ${IMAGE_NAME}:${BUILD_NUMBER} ${IMAGE_NAME}:latest
                """
            }
        }

        // stage('Docker Login') {
        //     steps {
        //         bat """
        //             echo "${DOCKER_CREDS_PSW}" | docker login -u "${DOCKER_CREDS_USR}" --password-stdin
        //         """
        //     }
        // }
        stage('Docker Login') {
            steps {
                withCredentials([usernamePassword(
                    credentialsId: 'dockerhub_credential',
                    usernameVariable: 'DOCKER_USER',
                    passwordVariable: 'DOCKER_PASS'
                )]) {
                    bat '''
                        echo %DOCKER_PASS% | docker login -u %DOCKER_USER% --password-stdin
                    '''
                }
            }
        }


        stage('Push Image to Registry') {
            steps {
                bat """
                    docker push ${IMAGE_NAME}:${BUILD_NUMBER}
                    docker push ${IMAGE_NAME}:latest
                """
            }
        }
    }

    post {
        success {
            echo "✅ Docker image pushed: ${IMAGE_NAME}:${BUILD_NUMBER}"
        }
        always {
            bat 'docker logout'
        }
    }
}


