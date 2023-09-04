pipeline {
    agent any

    environment {
        FTP_SERVER = 'ftp.kulmapeck.com'
        FTP_USER = 'kulma2146700'
        FTP_PASSWORD = 'sP2*9sB4s96XUz$'
        REMOTE_DIRECTORY = 'CICD'
    }

    stages {
          stage('Clone Kulmapeck Project') {
            steps {
                git branch: 'main', url: 'https://github.com/Sensei237/kulmapeck.git'
            }
        }

        stage('Installer Dependency') {
            steps {
                bat 'composer install'
            } 
        }

        stage('Enable update package') {
            steps {
                bat 'composer update'
            } 
        }

        stage('Clear symfony cache') {
            steps {
                bat 'php bin/console cache:clear'
            }
        }

        stage('Run test') {
            steps {
                bat 'php bin/phpunit'
            }
        }

        stage('Zip project') {
            steps {
                bat 'zip -r deployment.zip .'
            }
        }

        stage('Deployment FTP and push to Lws Server') {
            steps {
                bat "curl --ftp-create-dirs -T deployment.zip -u ${FTP_USER}:${FTP_PASSWORD} ftp://${FTP_SERVER}${REMOTE_DIRECTORY}/"
            }
        }

         stage('Rmmove Zip project') {
            steps {
                bat 'rm deployment.zip '
            }
        }

    }
}
