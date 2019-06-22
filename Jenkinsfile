node {
    stage("checkout") {
        git url: 'https://github.com/balapkm/circleci.git'
    }

    stage("last-changes") {
        def cmd = "php -m"
        sshagent(credentials : ['Balakumaran']) {
            sh "php -v"
        }
    }

    stage("check-changes") {
        sshagent(credentials : ['Balakumaran']) {
            sh "$cmd"
        }
    }
}
