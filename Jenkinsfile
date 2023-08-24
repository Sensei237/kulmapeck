pipeline {
    agent any

    stages {
        stage('Clone Kulmapeck Project') {
            steps {
                git 'https://github.com/Sensei237/kulmapeck.git'
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

        stage('Clear symfony cash') {

            steps {
                bat 'php bin/console cache:clear'
            }
        }

         stage('Run test') {
            
            steps {
                bat 'php bin/phpunit'
            }
        }

        stage('Deploiement FTP and push to Lws Server') {
            steps {
             bat 'curl -T  -u kulma2146700:sP2*9sB4s96XUz$ --ftp-ssl ftp://ftp.kulmapeck.com/'

            }
        }
    }
}
