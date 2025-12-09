package com.dhanuka.indhan.MyLeadMenu.activities.myleadactivity

import android.app.Activity
import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.LinearLayout
import android.widget.TextView
import androidx.localbroadcastmanager.content.LocalBroadcastManager
import androidx.viewpager.widget.ViewPager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.MyLeadMenu.adapters.MyLeadPagerAdapter
import com.dhanuka.indhan.R
import com.dhanuka.indhan.teamsummary.models.TeamSummaryDdDetail
import kotlinx.android.synthetic.main.activity_my_lead.*
import java.io.Serializable

class MyLeadActivity : BaseActivity() {
    var ddList: ArrayList<TeamSummaryDdDetail> = ArrayList()

    override fun onClick(v: View?) {
    }

    lateinit var context: MyLeadActivity
    private var ddID = ""
    var whichTabSelected = "open"

    var checkUserId = ""
    var cropId = ""
    var productId = ""
    var fromDate = ""
    var toDate = ""
    var status = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_my_lead)
        if (intent.hasExtra("ddID")) {
            ddID = intent?.getStringExtra("ddID") ?: ""
        }
        if (intent.hasExtra(Constants.DDLIST)) {
            ddList = intent.getSerializableExtra(Constants.DDLIST) as ArrayList<TeamSummaryDdDetail>
        }

        imgFilter.setOnClickListener {
            val i = Intent(this, MyLeadFilterActivity::class.java)
            i.putExtra(Constants.DDLIST, ddList as Serializable)
            i.putExtra("whichtabselected", whichTabSelected)
            startActivityForResult(i, 100)
        }


        context = this@MyLeadActivity
        //setup tabs
        setUpTabs()
    }

    //function for setup tabs
    private fun setUpTabs() {
        viewPagerFeeds.adapter =
            MyLeadPagerAdapter(this@MyLeadActivity, supportFragmentManager, ddID)
        viewPagerFeeds.offscreenPageLimit = 2
        tabLayout.setupWithViewPager(viewPagerFeeds)
        //[tab text small size]
        for (tabIndex in 0 until tabLayout.getTabCount()) {
            val tabTextView =
                ((tabLayout.getChildAt(0) as LinearLayout).getChildAt(tabIndex) as LinearLayout).getChildAt(
                    1
                ) as TextView
            tabTextView.setAllCaps(false)
        }


        viewPagerFeeds.addOnPageChangeListener(object : ViewPager.OnPageChangeListener {
            override fun onPageScrollStateChanged(state: Int) {
            }

            override fun onPageScrolled(
                position: Int,
                positionOffset: Float,
                positionOffsetPixels: Int
            ) {

            }

            override fun onPageSelected(position: Int) {
                if (position == 0) {
                    whichTabSelected = "open"
                } else if (position == 1) {
                    whichTabSelected = "close"
                }
            }

        })
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == 100) {

            if (resultCode == Activity.RESULT_OK) {
                data?.let {
                    if (it.hasExtra(Constants.CHECKED_USER_ID)) {
                        checkUserId = it.getStringExtra(Constants.CHECKED_USER_ID) ?: ""
                    }
                    if (it.hasExtra("cropid")) {
                        cropId = it.getStringExtra("cropid") ?: ""
                    }
                    if (it.hasExtra("productid")) {
                        productId = it.getStringExtra("productid") ?: ""
                    }
                    if (it.hasExtra("fromdate")) {
                        fromDate = it.getStringExtra("fromdate") ?: ""
                    }
                    if (it.hasExtra("todate")) {
                        toDate = it.getStringExtra("todate") ?: ""
                    }
                    if (it.hasExtra("status")) {
                        status = it.getStringExtra("status") ?: ""
                    }
                }
                if (status.equals("1")) {
                    val intent = Intent(Constants.FILTER)
                    intent.putExtra("checkedUserId", checkUserId)
                    intent.putExtra("cropid", cropId)
                    intent.putExtra("productid", productId)
                    intent.putExtra("fromdate", fromDate)
                    intent.putExtra("todate", toDate)
                    LocalBroadcastManager.getInstance(context).sendBroadcast(intent)
                } else {
                    val intent = Intent(Constants.FILTER_CLOSE)
                    intent.putExtra("checkedUserId", checkUserId)
                    intent.putExtra("cropid", cropId)
                    intent.putExtra("productid", productId)
                    intent.putExtra("fromdate", fromDate)
                    intent.putExtra("todate", toDate)
                    LocalBroadcastManager.getInstance(context).sendBroadcast(intent)
                }

            }
        }
    }
}
