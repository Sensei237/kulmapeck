pipeline {
    agent any

    environment {
        FTP_SERVER = ' ftp.staging-kulmapeck.online'
        FTP_USER = 'admin_staging-kulmapeck.online'
        FTP_PASSWORD = 'zbHa*Z$)jBH)Q'
        REMOTE_DIRECTORY = ''

        FTP_SERVER_Prod = 'vps96969.serveur-vps.net'
        FTP_USER_Prod = 'defaultpayment'
        FTP_PASSWORD_Prod = 'Benito@2000'
        REMOTE_DIRECTORY_Prod = '/web/CICD'
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
            bat 'del depl.zip'

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

                bat "ssh -p 5804 ${FTP_USER}@${FTP_SERVER} 'unzip -o ${REMOTE_DIRECTORY}/depl.zip -d ${REMOTE_DIRECTORY}'"
                
                // Remove the ZIP file on the remote server
                bat "ssh -p 5804 ${FTP_USER}@${FTP_SERVER} 'rm ${REMOTE_DIRECTORY}/depl.zip'"
                
            }
        }

        

    }
}
