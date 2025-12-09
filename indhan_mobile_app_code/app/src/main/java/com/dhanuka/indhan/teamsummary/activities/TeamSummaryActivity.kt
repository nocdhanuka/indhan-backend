package com.dhanuka.indhan.teamsummary.activities

import android.app.Activity
import android.content.Intent
import android.os.Bundle
import android.text.SpannableString
import android.text.style.UnderlineSpan
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.viewpager.widget.ViewPager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.acpfarmers.AcpFarmerActivity
import com.dhanuka.indhan.teamsummary.adapters.DdAdapter
import com.dhanuka.indhan.teamsummary.adapters.SummarySwipableAdapter
import com.dhanuka.indhan.teamsummary.models.*
import kotlinx.android.synthetic.main.activity_team_summary.*
import org.greenrobot.eventbus.EventBus
import org.joda.time.DateTime
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.text.SimpleDateFormat
import java.util.*

class TeamSummaryActivity : BaseActivity() {
    var list: ArrayList<TestUpcoming> = ArrayList()
    private var preference: Preference? = null
    var upcomingList: ArrayList<TeamSummaryUpcomingATP> = ArrayList()
    var ddDetailList: ArrayList<TeamSummaryDdDetail> = ArrayList()

    var getMonth = ""
    var getStartDate = ""
    var getEndDate = ""
    var checkedUserId = ""
    var plannedDate = ""

    var rsmId = ""
    var asmId = ""
    var flId = ""
    var isFirstTime = true


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_team_summary)
        preference = Preference(this)

        initializeViewPagerClippint()
        //apply click on view
        applyClickOnView()

        val content1 = SpannableString("ACP Farmers")
        content1.setSpan(UnderlineSpan(), 0, content1.length, 0)
        tvAcpFarmers.text = content1
        callTeamSummaryApi()

    }

    /**
     * apply click on view
     */

    private fun applyClickOnView() {

        imgFilterAll.setOnClickListener {
            // launchIntent(TeamSummaryFilterActivity::class.java,false)
            var intent = Intent(this@TeamSummaryActivity, TeamSummaryFilterActivity::class.java)
            startActivityForResult(intent, 100)
        }
        imgFilterUpcoming.setOnClickListener {
            var intent =
                Intent(this@TeamSummaryActivity, TeamSummaryUpcomingFilterActivity::class.java)
            //intent.putExtra(Constants.DDLIST, ddDetailList as Serializable)
            startActivityForResult(intent, 200)
            //launchIntent(TeamSummaryUpcomingFilterActivity::class.java, false)
            // launchIntent(DemoRequestEvaluatedActivity::class.java,false)

        }
        tvAcpFarmers.setOnClickListener {
            var intent = Intent(this@TeamSummaryActivity, AcpFarmerActivity::class.java)
            startActivity(intent)
            //launchIntent(TeamSummaryUpcomingFilterActivity::class.
        }
    }

    override fun onResume() {
        super.onResume()
        //call team summary API
        // callTeamSummaryApi()
    }

    override fun onClick(v: View?) {

    }

    /**
     * function for getting team summary Api
     */
    private fun callTeamSummaryApi() {
        showProgress()
        val c = Calendar.getInstance().time
        val df = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
        val formattedDate: String = df.format(c)

        val c1 = Calendar.getInstance()
        c1.time = DateTime.now().toDate()
        c1.add(Calendar.DATE, -30)
        val nextDate = c1.time
        val formattedFromDate: String = df.format(nextDate)

        val model: String = preference?.getString("token") ?: ""
        val query = HashMap<String, Any>()
        query["apiToken"] = model
        query["month"] = getMonth
        query["start_date"] = getStartDate
        query["end_date"] = getEndDate
        query["rsm"] = rsmId
        query["asm"] = asmId
        query["bdo"] = flId

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .getpTeamSummaryResponse(query).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<TeamSummaryResponseNew>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                    makeToast(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(listBean: TeamSummaryResponseNew) {
                    hideProgress()
                    if (listBean.status_code == 200) {
                        setDataForTeamSummary(listBean)
                    } else {
                        if (listBean.status_code == 409) {
                            EventBus.getDefault().post("logout")
                        }
                    }
                }
            })

    }

    /**
     * function for upcoming filter
     */
    private fun upcomingFilter() {
        showProgress()
        val model: String = preference?.getString("token") ?: ""
        val query = HashMap<String, Any>()
        query["apiToken"] = model
        query["upcoming_dds"] = checkedUserId
        query["plan_date"] = plannedDate
        query["rsm"] = rsmId
        query["asm"] = asmId
        query["bdo"] = flId

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).upcomingFilterApi(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<UpcomingResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                    makeToast(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(listBean: UpcomingResponse) {
                    hideProgress()
                    if (listBean.status_code == 200) {
                        upcomingList.clear()
                        upcomingList.addAll(listBean.data.upcomingATP)
                        if (upcomingList.size > 0) {
                            txtUpcomingNotFound.visibility = View.GONE
                            callSwipableAdapter(upcomingList)
                        } else {
                            callSwipableAdapter(upcomingList)
                            txtUpcomingNotFound.visibility = View.VISIBLE
                        }
                    } else {

                    }
                }
            })
    }

    /**
     * set Team summary data
     */
    private fun setDataForTeamSummary(listBean: TeamSummaryResponseNew) {
        txtDdCount.text = "" + listBean.data.ddCount
        txtVillageCount.text = "" + listBean.data.villageCount
        txtAcpFarmerCount.text = "" + listBean.data.farmerCount
        txtAverageVisits.text = "" + listBean.data.averageVisit
        if (isFirstTime) {
            upcomingList.clear()
            upcomingList.addAll(listBean.data.upcomingATP)
            if (upcomingList.size > 0) {
                txtUpcomingNotFound.visibility = View.GONE
                callSwipableAdapter(upcomingList)
            } else {
                callSwipableAdapter(upcomingList)
                txtUpcomingNotFound.visibility = View.VISIBLE
            }
        }

        ddDetailList.clear()
        ddDetailList.addAll(listBean.data.ddDetail)
        if (ddDetailList.size > 0) {
            txtNotFound.visibility = View.GONE
            setDdAdapter()
        } else {
            setDdAdapter()
            txtNotFound.visibility = View.VISIBLE
        }


    }

    //set dd name list
    private fun setDdAdapter() {
        rvDdList.layoutManager =
            LinearLayoutManager(this@TeamSummaryActivity)
        var adapter = DdAdapter(this@TeamSummaryActivity, ddDetailList)
        rvDdList.adapter = adapter
    }

    private fun callSwipableAdapter(list: ArrayList<TeamSummaryUpcomingATP>) {
        var summaryAdapter = SummarySwipableAdapter(this@TeamSummaryActivity, list)
        viewPager.adapter = summaryAdapter
    }


    private fun initializeViewPagerClippint() {
        //******* for padding first,second and third item at a time ****//
        viewPager.setClipToPadding(false)
        viewPager.setPadding(70, 0, 70, 0)
        viewPager.setPageMargin(15)
        viewPager.setPageTransformer(false, ViewPager.PageTransformer { page, position ->
            val pageWidth = viewPager.getMeasuredWidth() -
                    viewPager.getPaddingLeft() - viewPager.getPaddingRight()
            val pageHeight = viewPager.getHeight()
            val paddingLeft = viewPager.getPaddingLeft()
            val transformPos = (page.left - (viewPager.getScrollX() + paddingLeft)) / pageWidth
            var max = pageHeight / 10
            when {
                transformPos < -1 -> {
                    // This page is way off-screen to the left.
                    page.scaleY = 0.8f
                }
                transformPos <= 1 -> { // [-1,1]
                    page.scaleY = 1f
                }
                else -> { // (1,+Infinity]
                    // This page is way off-screen to the right.
                    page.scaleY = 0.8f
                }
            }
        })
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == 100) {
            if (resultCode == Activity.RESULT_OK) {
                data?.let {
                    getMonth = it.getStringExtra(Constants.GET_MONTH) ?: ""
                    getStartDate = it.getStringExtra(Constants.FROM) ?: ""
                    getEndDate = it.getStringExtra(Constants.TO_DATE) ?: ""
                    rsmId = it.getStringExtra("RSM") ?: ""
                    asmId = it.getStringExtra("ASM") ?: ""
                    flId = it.getStringExtra("FL") ?: ""
                }
                callTeamSummaryApi()
                isFirstTime = false

            }
        } else if (requestCode == 200) {
            if (resultCode == Activity.RESULT_OK) {
                data?.let {
                    if (it.hasExtra(Constants.CHECKED_USER_ID)) {
                        checkedUserId = it.getStringExtra(Constants.CHECKED_USER_ID) ?: ""
                        rsmId = it.getStringExtra("RSM") ?: ""
                        asmId = it.getStringExtra("ASM") ?: ""
                        flId = it.getStringExtra("FL") ?: ""
                    }
                    if (it.hasExtra(Constants.PLANED_DATE)) {
                        plannedDate = it.getStringExtra(Constants.PLANED_DATE) ?: ""
                    }
                    //Api for filter upcoming list
                    upcomingFilter()
                }
            }
        }
    }


}
