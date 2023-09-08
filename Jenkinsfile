pipeline {
    agent any

    environment {
        FTP_SERVER = 'ftp.kulmapeck.com'
        FTP_USER = '2146700mi44Et'
        FTP_PASSWORD = 'Staging@2023'
        REMOTE_DIRECTORY = ''
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
                // Use curl or any other appropriate method to decompress the uploaded ZIP file on the remote server
                bat "ssh -p 5804 ${FTP_USER}@${FTP_SERVER} 'unzip -o ${REMOTE_DIRECTORY}/depl.zip -d ${REMOTE_DIRECTORY}'"
                
                // Remove the ZIP file on the remote server
                bat "ssh -p 5804 ${FTP_USER}@${FTP_SERVER} 'rm ${REMOTE_DIRECTORY}/depl.zip'"
                
                // Remove the ZIP file locally
            }
        }

        

    }
}
