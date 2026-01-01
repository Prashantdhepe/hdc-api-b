pipeline {
  agent any

  options {
    timestamps()
    timeout(time: 40, unit: 'MINUTES')
    ansiColor('xterm')
  }

  environment {
    APP_ENV   = 'testing'
    APP_DEBUG = 'false'
    COMPOSER_CACHE_DIR = "${env.WORKSPACE}/.composer-cache"
  }

  stages {
    stage('Checkout') {
      steps {
        checkout scm
      }
    }

    stage('Composer Install & Lint (PHP)') {
      steps {
        script {
          docker.image('php:8.2-cli').inside('--user root') {
            sh '''
              apt-get update -y && apt-get install -y unzip git curl
              curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
              composer --version
              mkdir -p ${COMPOSER_CACHE_DIR}
              composer install --no-interaction --prefer-dist --no-progress --optimize-autoloader
              ./vendor/bin/pint --version || true
              ./vendor/bin/pint --no-interaction --verbose
              composer dump-autoload -o
            '''
          }
        }
      }
    }

    stage('Prepare .env') {
      steps {
        script {
          docker.image('php:8.2-cli').inside('--user root') {
            sh '''
              if [ ! -f .env ]; then
                cp .env.example .env
              fi
              php artisan key:generate --ansi
            '''
          }
        }
      }
    }

    stage('Node: Install & Build Assets') {
      steps {
        script {
          docker.image('node:20').inside {
            sh '''
              npm ci --no-audit --no-fund
              npm run build
            '''
          }
        }
      }
    }

    stage('Run Tests (PHPUnit)') {
      steps {
        script {
          docker.image('php:8.2-cli').inside('--user root') {
            sh '''
              # create junit output location
              mkdir -p storage/logs
              # run tests and generate junit.xml for Jenkins
              php artisan test --parallel --testsuite=Unit,Feature --log-junit=storage/logs/junit.xml || true
            '''
          }
        }
      }
    }

    stage('Report & Archive') {
      steps {
        script {
          // Publish JUnit, archive assets
          junit allowEmptyResults: true, testResults: 'storage/logs/junit.xml'
          archiveArtifacts artifacts: 'public/build/**', allowEmptyArchive: true
        }
      }
    }
  }

  post {
    success {
      echo '✅ CI PASSED: Laravel API pipeline completed successfully'
    }
    failure {
      echo '❌ CI FAILED: See logs and test reports'
    }
    always {
      cleanWs()
    }
  }
}