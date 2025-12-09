package com.dhanuka.indhan.TestimonialList

import android.net.Uri
import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import android.widget.MediaController
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.activity_web_view.*


class WebViewActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_web_view)

//        var webView=WebView(this)

        val uri = Uri.parse(intent.getStringExtra("key"))
        webView.setVideoURI(uri)
        webView.start()

//        webView.settings.javaScriptEnabled = true // enable javascript
//        webView.settings.javaScriptCanOpenWindowsAutomatically = true
//        webView.settings.pluginState = WebSettings.PluginState.ON
//        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.JELLY_BEAN_MR1) {
//            webView.settings.mediaPlaybackRequiresUserGesture = false
//        }
//        webView.webChromeClient = WebChromeClient()
//
//
//        webView.webViewClient = object : WebViewClient() {
//            override fun onReceivedError(view: WebView, errorCode: Int, description: String, failingUrl: String) {
//
//            }
//
//            @TargetApi(Build.VERSION_CODES.M)
//            override fun onReceivedError(view: WebView, req: WebResourceRequest, rerr: WebResourceError) {
//                onReceivedError(view, rerr.errorCode, rerr.description.toString(), req.url.toString())
//            }
//        }
//
//        webView.loadUrl(intent.getStringExtra("key"))
//        setContentView(webView)

        // create an object of media controller
        val mediaController = MediaController(this)
// initiate a video view
// set media controller object for a video view
        webView.setMediaController(mediaController)
    }

}
