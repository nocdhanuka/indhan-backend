package com.dhanuka.indhan.DemoRequestEvaluated.activities

import android.app.Activity
import android.content.BroadcastReceiver
import android.content.Context
import android.content.Intent
import android.content.IntentFilter
import android.os.Bundle
import android.view.View
import android.widget.LinearLayout
import android.widget.TextView
import androidx.localbroadcastmanager.content.LocalBroadcastManager
import androidx.viewpager.widget.ViewPager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DemoRequestEvaluated.adapters.DemoRequestEvaluatedAdapter
import com.dhanuka.indhan.DemoRequestEvaluated.models.DemoRequestEvaluatedResponse
import com.dhanuka.indhan.DemoRequestEvaluated.models.DemoRequestEvaluatedlist
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import kotlinx.android.synthetic.main.activity_demo_request_evaluated.*
import org.greenrobot.eventbus.EventBus
import org.joda.time.DateTime
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.text.SimpleDateFormat
import java.util.*

class DemoRequestEvaluatedActivity : BaseActivity() {
    private var preference: Preference? = null
    var demoRequestList: ArrayList<DemoRequestEvaluatedlist> = ArrayList()
    var list: ArrayList<String> = ArrayList()
    var productId = ""
    var cropId = ""
    var productName = ""
    var cropName = ""
    var from = ""//it check from where we are coming
    var apiToken: String = ""
    var villageId: String = ""
    var status = ""
    var fromDate = ""
    var toDate = ""
    var evaluatedCount = 0
    var evalueatedCount = "0"
    var awaitedCount = "0"
    var overDueCount = "0"
    var whichTabSelected = "Evaluated"
    var checkedUserId = ""

    private var countUpdate = object : BroadcastReceiver() {
        override fun onReceive(context: Context?, intent: Intent?) {
            callDemoRequestDataCountApi()
        }
    }

    override fun onClick(v: View?) {
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_demo_request_evaluated)
        preference = Preference(this)
        registerReceiver(countUpdate, IntentFilter("Data"))
        callDemoRequestDataApi(whichTabSelected)

        //click on fileter
        imgDemoFilter.setOnClickListener {
            val intent =
                Intent(this@DemoRequestEvaluatedActivity, DemoFilterEvaluteActivity::class.java)
            intent.putExtra("whichtabselected", whichTabSelected)
            intent.putExtra(Constants.FROM_DATE, fromDate)
            intent.putExtra(Constants.TO_DATE, toDate)
            intent.putExtra(Constants.PRODUCT_ID, productId)
            intent.putExtra(Constants.CROP_ID, cropId)
            intent.putExtra(Constants.PRODUCT_NAME, productName)
            intent.putExtra(Constants.CROP_NAME, cropName)
            startActivityForResult(intent, 100)
        }
    }

    //function for setup tabs
    private fun setUpTabs(evalueatedCount: String, awaitedCount: String, overDueCount: String) {
        viewPagerRerquest.adapter = DemoRequestEvaluatedAdapter(
            this@DemoRequestEvaluatedActivity,
            supportFragmentManager,
            tabLayout
        )
        viewPagerRerquest.offscreenPageLimit = 3
        tabLayout.setupWithViewPager(viewPagerRerquest)
        //[tab text small size]
        for (tabIndex in 0 until tabLayout.getTabCount()) {
            val tabTextView =
                ((tabLayout.getChildAt(0) as LinearLayout).getChildAt(tabIndex) as LinearLayout).getChildAt(
                    1
                ) as TextView
            tabTextView.setAllCaps(false)
        }
        viewPagerRerquest.addOnPageChangeListener(object : ViewPager.OnPageChangeListener {
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
                    whichTabSelected = "Evaluated"
                } else if (position == 1) {
                    whichTabSelected = "Awaited"
                } else if (position == 2) {
                    whichTabSelected = "Overdue"
                }
            }

        })
        setCountOnTabLayout(evalueatedCount, awaitedCount, overDueCount)
    }

    private fun setCountOnTabLayout(
        evaluatedCount: String,
        awaitedCount: String,
        overDueCount: String
    ) {
        tabLayout.getTabAt(0)?.setCustomView(R.layout.tab_evaluated)
        val txtEvaluatedId = tabLayout.findViewById(R.id.txtEvaluted) as TextView
        txtEvaluatedId.text = resources.getString(R.string.evaluated) + "(" + evaluatedCount + ")"

        tabLayout.getTabAt(1)?.setCustomView(R.layout.tab_awaited)
        val txtAwaitedId = tabLayout.findViewById(R.id.txtAwaited) as TextView
        txtAwaitedId.text = resources.getString(R.string.awaited) + "(" + awaitedCount + ")"

        tabLayout.getTabAt(2)?.setCustomView(R.layout.tab_overdue)
        val txtOverDueId = tabLayout.findViewById(R.id.txtOverDue) as TextView
        txtOverDueId.text = resources.getString(R.string.overdue) + "(" + overDueCount + ")"

    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == 100) {
            if (resultCode == Activity.RESULT_OK) {
                data?.let {
                    if (it.hasExtra(Constants.FROM)) {
                        from = it.getStringExtra(Constants.FROM) ?: ""
                    }
                    if (it.hasExtra(Constants.PRODUCT_ID)) {
                        productId = it.getStringExtra(Constants.PRODUCT_ID) ?: ""
                    }
                    if (it.hasExtra(Constants.CROP_ID)) {
                        cropId = it.getStringExtra(Constants.CROP_ID) ?: ""
                    }

                    if (it.hasExtra(Constants.FROM_DATE)) {
                        fromDate = it.getStringExtra(Constants.FROM_DATE) ?: ""
                    }
                    if (it.hasExtra(Constants.TO_DATE)) {
                        toDate = it.getStringExtra(Constants.TO_DATE) ?: ""
                    }
                    if (it.hasExtra(Constants.WHICHTAB_SELECTED)) {
                        whichTabSelected = it.getStringExtra(Constants.WHICHTAB_SELECTED) ?: ""
                        status = whichTabSelected
                    }
                    if (it.hasExtra(Constants.CHECKED_USER_ID)) {
                        checkedUserId = it.getStringExtra(Constants.CHECKED_USER_ID) ?: ""
                    }
                    if (it.hasExtra(Constants.PRODUCT_NAME)) {
                        productName = it.getStringExtra(Constants.PRODUCT_NAME) ?: ""
                    }
                    if (it.hasExtra(Constants.CROP_NAME)) {
                        cropName = it.getStringExtra(Constants.CROP_NAME) ?: ""
                    }
                }


                when (status) {
                    "Evaluated" -> {
                        val intent = Intent("Evaluated")
                        intent.putExtra(Constants.CHECKED_USER_ID, checkedUserId)
                        intent.putExtra("cropid", cropId)
                        intent.putExtra("productid", productId)
                        intent.putExtra("fromdate", fromDate)
                        intent.putExtra("todate", toDate)
                        LocalBroadcastManager.getInstance(this@DemoRequestEvaluatedActivity)
                            .sendBroadcast(intent)
                    }
                    "Awaited" -> {
                        val intent = Intent("Awaited")
                        intent.putExtra(Constants.CHECKED_USER_ID, checkedUserId)
                        intent.putExtra("cropid", cropId)
                        intent.putExtra("productid", productId)
                        intent.putExtra("fromdate", fromDate)
                        intent.putExtra("todate", toDate)
                        LocalBroadcastManager.getInstance(this@DemoRequestEvaluatedActivity)
                            .sendBroadcast(intent)
                    }
                    "Overdue" -> {
                        val intent = Intent("Overdue")
                        intent.putExtra(Constants.CHECKED_USER_ID, checkedUserId)
                        intent.putExtra("cropid", cropId)
                        intent.putExtra("productid", productId)
                        intent.putExtra("fromdate", fromDate)
                        intent.putExtra("todate", toDate)
                        LocalBroadcastManager.getInstance(this@DemoRequestEvaluatedActivity)
                            .sendBroadcast(intent)
                    }
                }
                callDemoRequestDataCountApi()
            }
        }
    }

    /**
     * function for getting Demo request Data
     */
    private fun callDemoRequestDataApi(status: String) {
        val c = Calendar.getInstance().time
        val df = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
        val formattedDate: String = df.format(c)

        val c1 = Calendar.getInstance()
        c1.time = DateTime.now().toDate()
        c1.add(Calendar.DATE, -30)
        val nextDate = c1.time
        val formattedFromDate: String = df.format(nextDate)

        //showProgress()
        val model: String = preference?.getString("token") ?: ""
        val query = HashMap<String, Any>()
        query["apiToken"] = model
        query["status"] = status
        query["from_date"] = fromDate
        query["to_date"] = toDate
        query["product"] = productId
        query["crop"] = cropId
        query["dd_ids"] = checkedUserId

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .getDemoRequestEvaluated(query).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<DemoRequestEvaluatedResponse>() {
                override fun onCompleted() {
                    //  hideProgress()
                }

                override fun onError(e: Throwable) {
                    // hideProgress()
                    makeToast("Not able to connect Please try again")
                }

                override fun onNext(listBean: DemoRequestEvaluatedResponse) {
                    if (listBean.status_code == 200) {
                        evaluatedCount =
                            listBean.data.Excellent + listBean.data.Average + listBean.data.Poor
                        evalueatedCount = "" + evaluatedCount
                        awaitedCount = "" + listBean.data.Awaited
                        overDueCount = "" + listBean.data.Overdue
                        val totalCount: Int =
                            evaluatedCount + listBean.data.Awaited + listBean.data.Overdue
                        title_demo_request.text =
                            resources.getString(R.string.demo_requests) + "(" + totalCount + ")"
                        setUpTabs(evalueatedCount, awaitedCount, overDueCount)
                    } else {
                        if (listBean.status_code == 409) {
                            EventBus.getDefault().post("logout")
                        }
                    }
                    hideProgress()
                }
            })

    }

    /**
     * function for getting Demo request Data
     */
    fun callDemoRequestDataCountApi() {
        //showProgress()
        val model: String = preference?.getString("token") ?: ""
        val query = HashMap<String, Any>()
        query["apiToken"] = model
        query["status"] = status
        query["from_date"] = fromDate
        query["to_date"] = toDate
        query["product"] = productId
        query["crop"] = cropId
        query["dd_ids"] = checkedUserId

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .getDemoRequestEvaluated(query).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<DemoRequestEvaluatedResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    // hideProgress()
                    makeToast("Not able to connect Please try again")
                }

                override fun onNext(listBean: DemoRequestEvaluatedResponse) {
                    // hideProgress()
                    if (listBean.status_code == 200) {
                        when {
                            status.equals("Evaluated") -> {
                                evaluatedCount =
                                    listBean.data.Excellent + listBean.data.Average + listBean.data.Poor
                                evalueatedCount = "" + evaluatedCount
                            }
                            status.equals("Awaited") -> {
                                awaitedCount = "" + listBean.data.Awaited
                            }
                            status.equals("Overdue") -> {
                                overDueCount = "" + listBean.data.Overdue
                            }
                        }
                        val totalCount: Int =
                            evaluatedCount + awaitedCount.toInt() + overDueCount.toInt()
                        title_demo_request.text =
                            resources.getString(R.string.demo_requests) + "(" + totalCount + ")"
                        setCountOnTabLayout(evalueatedCount, awaitedCount, overDueCount)
                    }
                }
            })

    }

    override fun onDestroy() {
        super.onDestroy()
        unregisterReceiver(countUpdate)
    }

}
