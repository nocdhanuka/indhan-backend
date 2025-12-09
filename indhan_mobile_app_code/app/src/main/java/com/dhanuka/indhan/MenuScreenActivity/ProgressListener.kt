package com.dhanuka.indhan.MenuScreenActivity

interface ProgressListener {
    fun showProgressDialog()
    fun dismissProgressDialog()
    fun updateProgress(progress: Int)
}