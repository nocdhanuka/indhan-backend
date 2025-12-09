package com.dhanuka.indhan.Activity.misAndReports

import android.annotation.SuppressLint
import android.app.DownloadManager
import android.net.Uri
import android.os.Build
import android.os.Bundle
import android.os.Environment
import android.text.TextUtils
import android.view.View
import android.webkit.WebChromeClient
import android.webkit.WebSettings
import android.webkit.WebView
import android.webkit.WebViewClient
import android.widget.ProgressBar
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.CheckConnectionStatus
import kotlinx.android.synthetic.main.webview_activity.*
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.GlobalScope
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import java.io.File


class WebViewAnyDataActivity : BaseActivity() {
    //    private var TAG = "WebViewAnyDataActivitySukhbeer"
    lateinit var progressBar: ProgressBar
    var url = ""
    var urlError = ""
    var downloadUrl = ""

    @SuppressLint("SetJavaScriptEnabled")
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.webview_activity)
        progressBar = progressBarWebView
        url = intent?.getStringExtra("key") ?: ""
        urlError = intent?.getStringExtra("key") ?: ""
        if (!TextUtils.isEmpty(intent?.getStringExtra("title") ?: "")) {
            tvTitle.text = intent?.getStringExtra("title") ?: ""
        }
        if (url.contains(".pdf")
            || url.contains(".ppt")
            || url.contains(".doc")
            || url.contains(".xls")
            || url.contains(".xps")
            || url.contains(".oxps")
            || url.contains(".html")
            || url.contains(".aspx")
            || url.contains(".txt")
        ) {
            url = "http://docs.google.com/viewer?embedded=true&url=$url"
        }

        ViewWebView.settings.javaScriptEnabled = true
        ViewWebView.settings.javaScriptCanOpenWindowsAutomatically = true
        ViewWebView.settings.supportZoom()
        ViewWebView.settings.setAppCacheEnabled(true)
        ViewWebView.settings.domStorageEnabled = true
        ViewWebView.zoomOut()
        ViewWebView.zoomIn()
        ViewWebView.settings.builtInZoomControls = true
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            ViewWebView.settings.mixedContentMode = WebSettings.MIXED_CONTENT_ALWAYS_ALLOW
        }
        ViewWebView.settings.loadWithOverviewMode = true
        ViewWebView.settings.useWideViewPort = true
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.JELLY_BEAN_MR1) {
            ViewWebView.settings.mediaPlaybackRequiresUserGesture = false
        }
        ViewWebView.settings.allowContentAccess = true
        ViewWebView.settings.allowFileAccess = true
        ViewWebView.settings.allowFileAccessFromFileURLs = true
        ViewWebView.settings.allowUniversalAccessFromFileURLs = true
        ViewWebView.settings.loadsImagesAutomatically = true
        ViewWebView.settings.loadWithOverviewMode = true
        ViewWebView.settings.pluginState = WebSettings.PluginState.ON
        ViewWebView.webViewClient = WebClient()
        ViewWebView.webChromeClient = MyWebChromeClient()
        ViewWebView.loadUrl(url)

        imgDownloadFile.setOnClickListener {
            if (CheckConnectionStatus.isInternetAvailable(this)) {
                downloadFile()
            } else {
                makeToast(resources.getString(R.string.no_internet_connection))
            }
        }
    }

    private fun downloadFile() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val uri = Uri.parse(downloadUrl)
                val destinationPath =
                    Environment.getExternalStorageDirectory().absolutePath + "/Indhan/"
                val image = "${uri.lastPathSegment}"
                val file = File(destinationPath)
                if (!file.exists()) {
                    file.mkdirs()
                }
                val file2 = File(file, image)
                if (!file2.exists()) {
                    val request = DownloadManager.Request(uri)
                    request.setAllowedNetworkTypes(DownloadManager.Request.NETWORK_WIFI or DownloadManager.Request.NETWORK_MOBILE)
                    request.setTitle(uri.lastPathSegment)
                    request.setNotificationVisibility(DownloadManager.Request.VISIBILITY_VISIBLE_NOTIFY_COMPLETED)
                    if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
                        request.setRequiresCharging(false)
                    }// Set if charging is required to begin the download
                    request.setAllowedOverMetered(true)// Set if download is allowed on Mobile network
                    request.setAllowedOverRoaming(true);// Set if download is allowed on roaming network
                    val fileUri = Uri.fromFile(file2)
                    request.setDestinationUri(fileUri)
                    val manager = getSystemService(DOWNLOAD_SERVICE) as DownloadManager
                    manager.enqueue(request)
                } else {
                    makeToast("File is already ")
                }
            }
        }
    }

    inner class MyWebChromeClient : WebChromeClient() {
        override fun onProgressChanged(view: WebView?, newProgress: Int) {
            progressBar.visibility = View.VISIBLE
            progressBar.progress = newProgress
        }

    }

    inner class WebClient : WebViewClient() {
        override fun shouldOverrideUrlLoading(view: WebView, url: String?): Boolean {
            if (!TextUtils.isEmpty(url)) {
                view.loadUrl(url!!)
            }
            return true
        }

        override fun onPageFinished(view: WebView?, url: String?) {
            super.onPageFinished(view, url)
            progressBar.visibility = View.GONE
            if (view?.title.equals("")) {
                view?.reload()
            }
        }

    }

    override fun onBackPressed() {
        if (ViewWebView.canGoBack()) {
            ViewWebView.goBack()
            progressBarWebView.visibility = View.GONE
        } else {
            super.onBackPressed()
        }
    }

    override fun onResume() {
        super.onResume()
        ViewWebView.onResume()
    }

    override fun onPause() {
        ViewWebView.onPause()
        super.onPause()
    }

    override fun onDestroy() {
        ViewWebView.onDestroy()
        super.onDestroy()
    }

    override fun onClick(v: View?) {
    }

}
