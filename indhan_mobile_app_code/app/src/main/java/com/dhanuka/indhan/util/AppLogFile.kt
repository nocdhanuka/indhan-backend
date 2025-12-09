package com.dhanuka.indhan.util

import com.dhanuka.indhan.App.appContext
import com.dhanuka.indhan.BuildConfig
import java.io.File
import java.io.FileWriter
import java.io.IOException

object AppLogFile {

    fun writeLineInFile(data: String, ecn: String) {
        val mediaStorageDir = File(
            appContext.cacheDir,
            BuildConfig.APPLICATION_ID
        )
        val mediaFile = File(
            mediaStorageDir.path + File.separator +
                    "${ecn}_indhanLogFile" + ".txt"
        )
        if (mediaFile.exists()) {
            try {
                val fr = FileWriter(mediaFile, true)
                fr.write("$data,")
                fr.close()
            } catch (e: IOException) {
                e.printStackTrace();
            }
        } else {
            try {
                mediaStorageDir.mkdirs()
                mediaFile.createNewFile()
                val fr = FileWriter(mediaFile, true)
                fr.write("$data,")
                fr.close()
            } catch (e: IOException) {
                e.printStackTrace();
            }
        }

    }
}