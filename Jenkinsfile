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
                sh 'composer install'
            }
        }

        stage('Build et tests') {
            steps {
                sh 'php bin/console cache:clear'
                sh 'php bin/phpunit'
            }
        }

        stage('Déploiement FTP') {
            steps {
                sh 'curl -T -u kulma2146700:sP2*9sB4s96XUz$ -ftp-ssl ftp://ftp.kulmapeck.com/'
            }
        }
    }
}
