node {
    stage("checkout") {
        git url: 'https://github.com/balapkm/circleci.git'
    }

    stage("last-changes") {
        def changeLogSets = currentBuild.changeSets
        for (int i = 0; i < changeLogSets.size(); i++) {
          def entries = changeLogSets[i].items
          for (int j = 0; j < entries.length; j++) {
            def entry = entries[j]
            def files = new ArrayList(entry.affectedFiles)
            for (int k = 0; k < files.size(); k++) {
              def file = files[k]
              println file.path
            }
          }
        }
    }
}
