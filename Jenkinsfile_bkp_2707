pipeline {
  agent any
  stages {
    stage('Clone') {
      steps {
        git 'https://github.com/balapkm/circleci.git'
        echo 'Hello'
      }
    }
  }
  environment {
    test = 'dev'
  }
}