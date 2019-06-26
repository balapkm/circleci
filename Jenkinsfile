pipeline {
  agent any
  stages {
    stage('Clone') {
      steps {
        git(changelog: true, url: 'https://github.com/balapkm/circleci.git', branch: 'masert')
      }
    }
  }
  environment {
    test = 'dev'
  }
}