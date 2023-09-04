pipeline {
    agent any

    environment {
        FTP_SERVER = 'vps96969.serveur-vps.net'
        FTP_USER = 'defaultbenito'
        FTP_PASSWORD = 'Benito@2000'
        REMOTE_DIRECTORY = '/CICD'
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
        powershell 'Compress-Archive -Path .\\* -DestinationPath deploy.zip'
           }
       }


        stage('Deployment FTP and push to Lws Server') {
            steps {
                bat "curl --ftp-create-dirs -T deploy.zip -u ${FTP_USER}:${FTP_PASSWORD} ftp://${FTP_SERVER}${REMOTE_DIRECTORY}/"
            }
        }

        

    }
}
