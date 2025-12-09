package com.dhanuka.indhan.View

import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.os.Bundle
import android.util.DisplayMetrics
import android.view.Gravity
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.view.Window.FEATURE_NO_TITLE
import android.widget.ProgressBar
import androidx.fragment.app.DialogFragment
import com.dhanuka.indhan.R

class OfflineProgressDialog : DialogFragment() {

    private lateinit var progressBar: ProgressBar

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        dialog?.requestWindowFeature(FEATURE_NO_TITLE)
        val view = inflater.inflate(R.layout.offline_progress_bar, null)
        progressBar = view.findViewById(R.id.progress)
        progressBar.progress = 0
        return view
    }

    fun setProgress(progress: Int) {
        var newProgress = 0
        newProgress = if (progress == 9) 20 else 10
        newProgress += progressBar.progress
        progressBar.progress = newProgress
        if (progressBar.progress == 100) {
            if (isVisible && isAdded) {
                dismiss()
            }
        }

    }

    override fun onActivityCreated(savedInstanceState: Bundle?) {
        super.onActivityCreated(savedInstanceState)
        val metrics = DisplayMetrics()
        activity?.windowManager?.defaultDisplay?.getMetrics(metrics)
        dialog?.window?.setGravity(Gravity.CENTER)
        dialog?.window?.setLayout(
            ViewGroup.LayoutParams.MATCH_PARENT,
            ViewGroup.LayoutParams.MATCH_PARENT
        )
        dialog?.window?.setBackgroundDrawable(ColorDrawable(Color.WHITE))
    }
}