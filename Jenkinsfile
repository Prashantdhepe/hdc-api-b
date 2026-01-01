pipeline {
  agent any

  options {
    timestamps()
    timeout(time: 40, unit: 'MINUTES')
  }

  parameters {
    booleanParam(name: 'MANUAL_TRIGGER', defaultValue: false, description: 'Set true to run CI; if false, main stages will be skipped (prevents automatic triggers)')
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

    stage('Manual Gate') {
      steps {
        script {
          if (!params.MANUAL_TRIGGER) {
            echo 'MANUAL_TRIGGER=false — main stages will be skipped. To run, re-run and set MANUAL_TRIGGER=true.'
          } else {
            echo 'MANUAL_TRIGGER=true — proceeding with pipeline.'
          }
        }
      }
    }

    stage('Composer Install & Lint (PHP)') {
      when { expression { return params.MANUAL_TRIGGER == true } }
      steps {
        script {
          docker.image('php:8.2-cli').inside('--user root') {
            bat '''
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
      when { expression { return params.MANUAL_TRIGGER == true } }
      steps {
        script {
          docker.image('php:8.2-cli').inside('--user root') {
            bat '''
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
      when { expression { return params.MANUAL_TRIGGER == true } }
      steps {
        script {
          docker.image('node:20').inside {
            bat '''
              npm ci --no-audit --no-fund
              npm run build
            '''
          }
        }
      }
    }

    stage('Run Tests (PHPUnit)') {
      when { expression { return params.MANUAL_TRIGGER == true } }
      steps {
        script {
          docker.image('php:8.2-cli').inside('--user root') {
            bat '''
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
      when { expression { return params.MANUAL_TRIGGER == true } }
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