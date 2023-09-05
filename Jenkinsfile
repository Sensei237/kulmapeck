pipeline {
    agent any

    environment {
        FTP_SERVER = 'vps96969.serveur-vps.net'
        FTP_USER = 'defaultpayment'
        FTP_PASSWORD = 'Benito@2000'
        REMOTE_DIRECTORY = '/var/www/clients/client0/web16/CICD/'
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

        stage('Clear Symfony Cache') {
            steps {
                bat 'php bin/console cache:clear --env=prod'
            }
        }

        stage('Run test') {
            steps {
                bat 'php bin/phpunit'
            }
        }

       stage('Zip project') {
          steps {
        powershell 'Compress-Archive -Path .\\* -DestinationPath depl.zip'
           }
       }


        stage('Deployment FTP and push to Lws Server') {
            steps {
                bat "curl --ftp-create-dirs -T depl.zip -u ${FTP_USER}:${FTP_PASSWORD} ftp://${FTP_SERVER}${REMOTE_DIRECTORY}/"
            }
        }

        stage('Decompress project on remote server') {
            steps {
                // Use curl or any other appropriate method to decompress the uploaded ZIP file on the remote server
                bat "ssh ${FTP_USER}@${FTP_SERVER} 'unzip -o ${REMOTE_DIRECTORY}/depl.zip -d ${REMOTE_DIRECTORY}'"
                
                // Remove the ZIP file on the remote server
                bat "ssh ${FTP_USER}@${FTP_SERVER} 'rm ${REMOTE_DIRECTORY}/depl.zip'"
                
                // Remove the ZIP file locally
                bat 'rm depl.zip'
            }
        }

        

    }
}
