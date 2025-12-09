package com.dhanuka.indhan.Activity

import android.graphics.Color
import android.os.Build
import android.os.Bundle
import android.view.View
import android.view.WindowManager
import com.dhanuka.indhan.MenuScreenActivity.View.DynamicMenuActivity
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R


/**
 * Created by Shivam on 2/20/2018.
 */

class SplashActivity : BaseActivity() {

    private lateinit var preference: Preference

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        val win = window
        win.addFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN)
        // Transparent Status Bar
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            window.decorView.systemUiVisibility =
                View.SYSTEM_UI_FLAG_LAYOUT_STABLE or View.SYSTEM_UI_FLAG_LAYOUT_FULLSCREEN
            window.statusBarColor = Color.TRANSPARENT
        }
        setContentView(R.layout.activity_splash)
        preference = Preference.getInstance(this)

        val timerThread = object : Thread() {
            override fun run() {
                try {
                    Thread.sleep(2000)
                } catch (e: InterruptedException) {
                    e.printStackTrace()
                } finally {
                    if (preference.getBoolean("isLogin")) {
                        //  launchIntent(HomeActivity::class.java, true)
//                        launchIntent(testClassActivity::class.java, true)
                        launchIntent(DynamicMenuActivity::class.java, true)
                    } else {
                        launchIntent(LoginActivity::class.java, true)
                    }
                }
            }
        }
        timerThread.start()
    }

    override fun onClick(view: View) {

    }
}
