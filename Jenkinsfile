pipeline {
    agent any

    stages {
        stage('Cloner le code') {
            steps {
                git 'https://github.com/Sensei237/kulmapeck.git'
            }
        }

        stage('Installer les dépendances') {
            steps {
                bat 'composer install'
            }
        }

        stage('Build et tests') {
            steps {
                bat 'php bin/console cache:clear'
                bat 'php bin/phpunit'
            }
        }

        stage('Déploiement FTP') {
            steps {
                bat 'curl -T -u kulma2146700:sP2*9sB4s96XUz$ -ftp-ssl ftp://ftp.kulmapeck.com/'
            }
        }
    }
}
