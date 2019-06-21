pipeline {
    agent any
    stages {
        stage('Example') {
            input {
                message "Should we continue?"
                ok "Yes, we should."
                submitter "vimal"
                parameters {
                    string(name: 'PERSON', defaultValue: 'Mr Jenkins', description: 'Who should I say hello to?')
                }
            }
            steps {
                echo "Hello, ${PERSON}, nice to meet you."
            }
            def changeLogSets = currentBuild.changeSets
            for (int i = 0; i < changeLogSets.size(); i++) {
                def entries = changeLogSets[i].items
                for (int j = 0; j < entries.length; j++) {
                    def entry = entries[j]
                    echo "${entry.commitId} by ${entry.author} on ${new Date(entry.timestamp)}: ${entry.msg}"
                    def files = new ArrayList(entry.affectedFiles)
                    for (int k = 0; k < files.size(); k++) {
                        def file = files[k]
                        echo "  ${file.editType.name} ${file.path}"
                    }
                }
            }
        }
    }
}
