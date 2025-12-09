package com.dhanuka.indhan.institutional.activity.influencers

import android.os.Bundle
import android.os.Handler
import android.os.Looper
import android.text.TextUtils
import android.view.View
import com.dhanuka.indhan.ATPMenu.activity.models.CommonAtpResponse
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.institutional.adapter.influencers.InfluencersAddAtpAdapter
import com.dhanuka.indhan.institutional.model.RefreshEventModel
import com.dhanuka.indhan.institutional.model.influencers.*
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.plan_new_atp_influencer_activity.*
import kotlinx.coroutines.*
import noman.weekcalendar.listener.OnDateClickListener
import org.greenrobot.eventbus.EventBus
import org.joda.time.DateTime
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*
import kotlin.math.pow

class InfluencersPlanNewAtpActivity : BaseActivity(),
    InfluencersAddAtpAdapter.OrClickListener, OnDateClickListener {

    private var flEcn: String = ""
    private var flName: String = ""
    private var influencersId: String = ""
    private var categoryId: String = "0"
    private var ticketId: String = ""
    private var date = ""

    val listOfSelectedDated: ArrayList<DateTime> = ArrayList()
    private var influencersAddAdapter: InfluencersAddAtpAdapter? = null
    private var arrayListAtpData: ArrayList<InfluencersPlanAtpModel> = ArrayList()
    private var influencerCopyList: ArrayList<InfluencersData> = ArrayList()
    private var arrayListCategory: ArrayList<InfluencersCategoryData> = ArrayList()

    private var upComingPlannedId = ""
    private var flDataList: ArrayList<InfluencersFlData> = ArrayList()
    private var influencersList: ArrayList<InfluencersData> = ArrayList()
    private var categoryData: ArrayList<String> = ArrayList()
    private var preference: Preference? = null
    private var userId = ""
    private var userType = ""
    private var appDatabase: AppDatabase? = null
    private var selectInfluencers: NumberSpinner? = null
    private var selectFl: NumberSpinner? = null

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.submit -> {
                submitAtpData()
            }
            R.id.iCross -> {
                finish()
            }
            R.id.tvReAddNewActivity -> {
                saveAtp()
            }
            R.id.spinner_estate_source -> {
                selectInfluencers?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }
            }
            R.id.spinner_dd -> {
                selectFl?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }
            }
        }
    }

    private fun submitAtpData() {
        submit.isEnabled = false
        var data: InfluencersPlanAtpApiModel? = null
        val atpDataList: ArrayList<InfluencersAtpData> = ArrayList()
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                appDatabase?.influencersPlanAtpModelDao()?.getAllAddPlanAtpModelData()?.forEach {
                    val atpData = InfluencersAtpData(
                        date_time = it.dateTime,
                        influencer_category = it.companyCategory,
                        fl_ecn = it.flEcn,
                        fl_name = it.flName,
                        fl_type = it.flType,
                        is_manager = it.manager,
                        influencer_id = it.influencerId,
                        ticket_number = it.ticketNumber
                    )
                    atpDataList.add(atpData)
                }
                delay(300)
                data =
                    InfluencersPlanAtpApiModel(
                        apiToken = preference?.getString("token") ?: "",
                        atp_data = atpDataList
                    )

            }.run {
                withContext(Dispatchers.Main) {
                    if (data != null) {
                        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
                            .planAtpInfluencersSubmit(data)
                            .subscribeOn(Schedulers.newThread())
                            .observeOn(AndroidSchedulers.mainThread())
                            .subscribe(object : Subscriber<CommonAtpResponse>() {
                                override fun onCompleted() {
                                    hideProgress()
                                    submit.isEnabled = true
                                }

                                override fun onError(e: Throwable) {
                                    hideProgress()
                                    makeToast("Failure")
                                    upDateRcView()
                                    submit.isEnabled = true
                                    restoreDefault()
                                    updateCalenderWithSelectedTime()
                                    calendar.setStartDate(
                                        DateTime().plusDays(0),
                                        listOfSelectedDated
                                    )
                                }

                                override fun onNext(homeBean: CommonAtpResponse?) {
                                    hideProgress()
                                    submit.isEnabled = true
                                    if (homeBean?.status_code == 200) {
                                        GlobalScope.launch {
                                            withContext(Dispatchers.IO) {
                                                appDatabase?.influencersPlanAtpModelDao()
                                                    ?.deleteAllPlanAtpModelData()
                                            }
                                        }
                                        hideProgress()
                                        makeToast((homeBean.message).plus(", Please refresh this screen."))
                                        EventBus.getDefault()
                                            .post(
                                                RefreshEventModel(
                                                    data = 2,
                                                    page = "Influencers_atp"
                                                )
                                            )
                                        finish()
                                    } else {
                                        hideProgress()
                                    }
                                }
                            })
                    } else {
                        submit.isEnabled = true
                    }
                }
            }
        }
    }

    private fun upDateRcView() {
        arrayListAtpData.clear()
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                arrayListAtpData.addAll(
                    appDatabase?.influencersPlanAtpModelDao()?.getAllAddPlanAtpModelData(0)
                        ?: ArrayList<InfluencersPlanAtpModel>()
                )
            }
        }
        influencersAddAdapter?.notifyItemChanged(arrayListAtpData.size)
    }

    private fun restoreDefault() {
        spinner_estate_source.text = ""
        if (userType.contains("bdo", ignoreCase = true)) {
            spinner_dd.text = "My self"
            flName = "My self"
            flEcn = "${preference?.getString("ecn")}"
        } else {
            spinner_dd.text = ""
            flEcn = "0"
            flName = "My Self"
        }
        checkboxManager.isChecked = false
        ticketId = ""
        influencersId = ""
    }

    private fun saveAtp() {
        val resp = checkValues()
        if (resp.equals("perfect", ignoreCase = true)) {
            val model = InfluencersPlanAtpModel()
            ticketId = "PV3${randomInt(2)}${randomInt(2)}${randomInt(2)}${randomInt(1)}"
            model.apiToken = preference?.getString("token") ?: ""
            model.status = 0
            model.hitTime = System.currentTimeMillis()
            model.dateTime = date
            model.flType = "bdo"
            model.flEcn = flEcn
            model.flName = flName
            model.ticketNumber = ticketId
            model.influencerId = influencersId
            model.companyCategory = categoryId
            model.manager = when {
                checkboxManager.isChecked -> "1"
                flName.equals(
                    "My Self",
                    ignoreCase = true
                ) -> "0"
                else -> "0"
            }
            arrayListAtpData.add(model)
            influencersAddAdapter?.notifyItemChanged(arrayListAtpData.size)
            saveDataLocallyAndClear(model)
            calendar.setStartDate(DateTime().plusDays(0), listOfSelectedDated)
        } else {
            makeToast(resp)
        }
    }

    private fun saveDataLocallyAndClear(model: InfluencersPlanAtpModel) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                appDatabase?.influencersPlanAtpModelDao()?.insert(model)
            }.run {
                withContext(Dispatchers.Main) {
                    restoreDefault()
                }
            }
        }
    }

    fun checkValues(): String {
        return when {
            TextUtils.isEmpty(date) -> {
                "Please Select date of plan atp."
            }
            TextUtils.isEmpty(flEcn) -> {
                "Please Select Fl for atp."
            }
            TextUtils.isEmpty(categoryId) -> {
                "Please Select category."
            }
            TextUtils.isEmpty(influencersId) -> {
                "Please Select Influencers/Branch for atp plan."
            }
            else -> "perfect"
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.plan_new_atp_influencer_activity)
        updateCalenderWithSelectedTime()
        preference = Preference(this)
        userId = preference?.getString("user_id") ?: ""
        userType = preference?.getString("user_type") ?: ""
        intent?.getStringExtra("from")?.let {
            upComingPlannedId = it
        }
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        date = DateTime().plusDays(0).toString()
        getLocalData()
        setOnClicks()
        setAdapter()
        if (userType.contains("bdo", ignoreCase = true)) {
            llCheckBox.visibility = View.GONE
        } else {
            llCheckBox.visibility = View.VISIBLE
        }
    }

    private fun getLocalData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                if (!userType.equals("bdo", ignoreCase = true)) {
                    flDataList.clear()
                    (appDatabase?.flDataListInfluencersDao()?.getAllFlByManagerEcn(userId)
                        ?: ArrayList<InfluencersFlData>()).forEach() {
                        flDataList.add(it!!)
                    }
                }

                influencersList.clear()
                (appDatabase?.influencersListDataDao()?.allInfluencers
                    ?: ArrayList<InfluencersData>()).forEach() {
                    influencersList.add(it!!)
                    influencerCopyList.add(it)
                }
                categoryData.clear()
                ((appDatabase?.categoryDataInfluencersDao()?.allCategoryData
                    ?: ArrayList<InfluencersCategoryData>()) as ArrayList<InfluencersCategoryData>).forEach() {
                    arrayListCategory.add(it)

                }
                for ((index) in arrayListCategory.withIndex()) {
                    if (arrayListCategory[index].company_category
                            .equals(upComingPlannedId, ignoreCase = true)
                    ) {
                        categoryId = arrayListCategory[index].influencer_category_id.toString()
                        break
                    }
                }
            }.run {
                withContext(Dispatchers.Main) {
                    setDropDownData()
                }
            }
        }
    }

    private fun setDropDownData() {
        if (!userType.equals("bdo", ignoreCase = true)) {
            flEcn = "0"
            flName = "My Self"
            val flList = ArrayList<String>()
            flDataList.forEach() {
                it.name.let { it1 -> flList.add(it1) }
            }
            selectFl = NumberSpinner(this, flList, "Assign FL")
            selectFl?.bindOnSpinerListener(object : OnSpinerItemClick {
                override fun onClick(item: String, position: Int) {
                    if (flDataList.size > 0) {
                        spinner_dd.text = item
                        flEcn = flDataList[position].ECN
                        flName = flDataList[position].name
                        setInfluencersDropDown(categoryId, flEcn)
                    }
                }
            })
        } else {
            spinner_dd.text = "My Self"
            flEcn = "${preference?.getString("ecn")}"
            flName = "My Self"
        }
        setInfluencersDropDown(categoryId, null)
    }

    private fun setInfluencersDropDown(categoryId: String, flECN: String?) {
        val influencersStringList = ArrayList<String>()
        if (flECN == null) {
            influencersList.forEach() {
                it.name?.let { it1 -> influencersStringList.add(it1) }
            }
        } else {
            influencerCopyList.forEach() {
                if (flECN.equals(it.user_ecn, ignoreCase = true)) {
                    it.name?.let { it1 -> influencersStringList.add(it1) }
                }
            }
        }

        selectInfluencers = NumberSpinner(this, influencersStringList, "Select Influencer")
        selectInfluencers?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinner_estate_source.text = item
                if (flECN == null) {
                    for ((index) in influencersList.withIndex()) {
                        if (influencersList[index].name == item) {
                            influencersId = influencersList[index].influencer_id.toString()
                            break
                        }
                    }
                } else {
                    for ((index) in influencerCopyList.withIndex()) {
                        if (influencerCopyList[index].name == item) {
                            influencersId = influencerCopyList[index].influencer_id.toString()
                            break
                        }
                    }
                }
            }
        })
    }

    private fun randomInt(digits: Int): Int {
        val minimum = 10.0.pow(digits - 1.toDouble()).toInt()
        val maximum = 10.0.pow(digits.toDouble()).toInt() - 1
        val random = Random()
        return minimum + random.nextInt(maximum - minimum + 1)
    }

    private fun setOnClicks() {
        submit.setOnClickListener(this)
        calendar.setOnDateClickListener(this)
        tvReAddNewActivity.setOnClickListener(this)
        iCross.setOnClickListener(this)
        spinner_dd.setOnClickListener(this)
        spinner_estate_source.setOnClickListener(this)
        calendar.setStartDate(DateTime().plusDays(0), listOfSelectedDated)
        calendar.setSelectedDate(DateTime().plusDays(0))
        tvMonth.text = Utility.getDateCurrent(DateTime().plusDays(0))
    }

    private fun setAdapter() {
        influencersAddAdapter =
            InfluencersAddAtpAdapter(arrayListAtpData, this@InfluencersPlanNewAtpActivity)
        rvListPlanAtpList.adapter = influencersAddAdapter
        influencersAddAdapter?.setLongClickListener(this)
    }

    override fun onLongClick(view: View, atpDbModel: InfluencersPlanAtpModel, position: Int) {
        if (arrayListAtpData.find { it.id == atpDbModel.id } != null) {
            arrayListAtpData.remove(arrayListAtpData.find { it.id == atpDbModel.id })
        }
        influencersAddAdapter?.notifyItemRemoved(position)
    }

    override fun onDateClick(dateTime: DateTime?) {
        if (dateTime?.monthOfYear() != null && dateTime.year() != null) {
            if (dateTime.monthOfYear().get() > DateTime().monthOfYear().get() + 1) {
                calendar.reset()
                makeToast("You can only plan for 1 month in advance.")
                Handler(Looper.getMainLooper()).postDelayed({ }, 300)
                return
            }
        }
        if (dateTime?.monthOfYear() != null && dateTime.year() != null) {
            if (dateTime.plusHours(1) < DateTime()) {
                calendar.reset()
                date = DateTime().plusDays(0).toString();
                makeToast("You can not plan for past dates")
                Handler(Looper.getMainLooper()).postDelayed({
                    updateCalenderWithSelectedTime()
                    calendar.setStartDate(DateTime().plusDays(0), listOfSelectedDated)
                    calendar.setSelectedDate(DateTime().plusDays(0))
                    (DateTime().plusDays(0).dayOfMonth()
                        .getAsText(Locale.ENGLISH) + " " + DateTime().monthOfYear()
                        .getAsText(Locale.ENGLISH) + ", " + DateTime().year()
                        .getAsText(Locale.ENGLISH)).also { tvMonth.text = it }
                }, 300)
                return
            }
        }

//        if (dateTime?.monthOfYear() != null && dateTime.year() != null) {
//            if (dateTime <= DateTime().minusDays(0)) {
//                calendar.reset()
//                date = DateTime().plusDays(0).toString();
//                makeToast("You can not plan for current date")
//                Handler(Looper.getMainLooper()).postDelayed({
//                    updateCalenderWithSelectedTime()
//                    calendar.setStartDate(DateTime().plusDays(0), listOfSelectedDated)
//                    calendar.setSelectedDate(DateTime().plusDays(0))
//                    (DateTime().plusDays(0).dayOfMonth()
//                        .getAsText(Locale.ENGLISH) + " " + DateTime().monthOfYear()
//                        .getAsText(Locale.ENGLISH) + ", " + DateTime().year()
//                        .getAsText(Locale.ENGLISH)).also { tvMonth.text = it }
//                }, 300)
//                return
//            }
//        }
        if (!(dateTime?.monthOfYear() == null || dateTime.year() == null)) {
            (dateTime.dayOfMonth().getAsText(Locale.ENGLISH) + " " + dateTime.monthOfYear()
                .getAsText(Locale.ENGLISH) + ", " + dateTime.year()
                .getAsText(Locale.ENGLISH)).also { tvMonth.text = it }

            date = dateTime.toString()
        }
    }

    private fun updateCalenderWithSelectedTime() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                listOfSelectedDated.clear()
                appDatabase?.influencersPlanAtpModelDao()?.getAllAddPlanAtpModelData()?.forEach {
                    val time = DateTime(it.dateTime)
                    listOfSelectedDated.add(time)
                }
            }
        }
    }
}