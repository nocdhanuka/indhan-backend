package com.dhanuka.indhan.institutional.activity.sugarMill

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
import com.dhanuka.indhan.institutional.adapter.sugarMill.SugarMillAddAtpAdapter
import com.dhanuka.indhan.institutional.model.RefreshEventModel
import com.dhanuka.indhan.institutional.model.sugarMill.*
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.plan_new_atp_sugarmill_activity.*
import kotlinx.coroutines.*
import noman.weekcalendar.listener.OnDateClickListener
import org.greenrobot.eventbus.EventBus
import org.joda.time.DateTime
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*
import kotlin.math.pow

class SugarMillPlanNewAtpActivity : BaseActivity(),
    SugarMillAddAtpAdapter.OrClickListener, OnDateClickListener {

    private var flEcn: String = ""
    private var flName: String = ""
    private var sugarMillId: String = ""
    private var categoryId: String = "0"
    private var ticketId: String = ""
    private var date = ""

    val listOfSelectedDated: ArrayList<DateTime> = ArrayList()
    private var sugarMillAddAdapter: SugarMillAddAtpAdapter? = null
    private var arrayListAtpData: ArrayList<SugarMillPlanAtpModel> = ArrayList()
    private var sugarMillCopyList: ArrayList<SugarMillData> = ArrayList()
    private var arrayListCategory: ArrayList<SugarMillCategoryData> = ArrayList()
    private var upComingPlannedId = ""
    private var flDataList: ArrayList<SugarMillFlData> = ArrayList()
    private var sugarMillList: ArrayList<SugarMillData> = ArrayList()
    private var categoryData: ArrayList<String> = ArrayList()
    private var preference: Preference? = null
    private var userId = ""
    private var userType = ""
    private var appDatabase: AppDatabase? = null
    private var selectSugarMill: NumberSpinner? = null
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
                selectSugarMill?.let {
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
        var data: SugarMillPlanAtpApiModel? = null
        val atpDataList: ArrayList<SugarMillAtpData> = ArrayList()
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                appDatabase?.sugarMillPlanAtpModelDao()?.getAllAddPlanAtpModelData()?.forEach {
                    val atpData = SugarMillAtpData(
                        date_time = it.dateTime,
                        sugarmill_category = it.companyCategory,
                        fl_ecn = it.flEcn,
                        fl_name = it.flName,
                        fl_type = it.flType,
                        is_manager = it.manager,
                        sugarmill_id = it.sugarMillId,
                        ticket_number = it.ticketNumber
                    )
                    atpDataList.add(atpData)
                }
                delay(300)
                data =
                    SugarMillPlanAtpApiModel(
                        apiToken = preference?.getString("token") ?: "",
                        atp_data = atpDataList
                    )

            }.run {
                withContext(Dispatchers.Main) {
                    if (data != null) {
                        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
                            .planAtpSugarMillSubmit(data)
                            .subscribeOn(Schedulers.newThread())
                            .observeOn(AndroidSchedulers.mainThread())
                            .subscribe(object : Subscriber<CommonAtpResponse>() {
                                override fun onCompleted() {
                                    submit.isEnabled = true
                                    hideProgress()
                                }

                                override fun onError(e: Throwable) {
                                    hideProgress()
                                    submit.isEnabled = true
                                    makeToast("Failure")
                                    upDateRcView()
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
                                                appDatabase?.sugarMillPlanAtpModelDao()
                                                    ?.deleteAllPlanAtpModelData()
                                            }
                                        }
                                        hideProgress()
                                        makeToast((homeBean.message).plus(", Please refresh this screen."))
                                        EventBus.getDefault()
                                            .post(
                                                RefreshEventModel(
                                                    data = 2,
                                                    page = "sugarmill_atp"
                                                )
                                            )
                                        finish()
                                    } else {
                                        hideProgress()
                                        submit.isEnabled = true
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
                    appDatabase?.sugarMillPlanAtpModelDao()?.getAllAddPlanAtpModelData(0)
                        ?: ArrayList<SugarMillPlanAtpModel>()
                )
            }
        }
        sugarMillAddAdapter?.notifyItemChanged(arrayListAtpData.size)
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
        sugarMillId = ""
    }

    private fun saveAtp() {
        val resp = checkValues()
        if (resp.equals("perfect", ignoreCase = true)) {
            val model = SugarMillPlanAtpModel()
            ticketId = "PV3${randomInt(2)}${randomInt(2)}${randomInt(2)}${randomInt(1)}"
            model.apiToken = preference?.getString("token") ?: ""
            model.status = 0
            model.hitTime = System.currentTimeMillis()
            model.dateTime = date
            model.flType = "bdo"
            model.flEcn = flEcn
            model.flName = flName
            model.ticketNumber = ticketId
            model.sugarMillId = sugarMillId
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
            sugarMillAddAdapter?.notifyItemChanged(arrayListAtpData.size)
            saveDataLocallyAndClear(model)
            calendar.setStartDate(DateTime().plusDays(0), listOfSelectedDated)
        } else {
            makeToast(resp)
        }
    }

    private fun saveDataLocallyAndClear(model: SugarMillPlanAtpModel) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                appDatabase?.sugarMillPlanAtpModelDao()?.insert(model)
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
            TextUtils.isEmpty(sugarMillId) -> {
                "Please Select SugarMill/Branch for atp plan."
            }
            else -> "perfect"
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.plan_new_atp_sugarmill_activity)
        updateCalenderWithSelectedTime()
        preference = Preference(this)
        userId = preference?.getString("user_id") ?: ""
        userType = preference?.getString("user_type") ?: ""
        intent?.getStringExtra("from")?.let {
            upComingPlannedId = it
        }
        if (userType.contains("bdo", ignoreCase = true)) {
            llCheckBox.visibility = View.GONE
        } else {
            llCheckBox.visibility = View.VISIBLE
        }
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        date = DateTime().plusDays(0).toString()
        getLocalData()
        setOnClicks()
        setAdapter()
    }

    private fun getLocalData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                if (!userType.equals("bdo", ignoreCase = true)) {
                    flDataList.clear()
                    (appDatabase?.flDataListSugarMillDao()?.getAllFlByManagerEcn(userId)
                        ?: ArrayList<SugarMillFlData>()).forEach() {
                        flDataList.add(it!!)
                    }
                }

                sugarMillList.clear()
                (appDatabase?.sugarMillListDataDao()?.allSugarMill
                    ?: ArrayList<SugarMillData>()).forEach() {
                    sugarMillList.add(it!!)
                    sugarMillCopyList.add(it)
                }
                categoryData.clear()
                ((appDatabase?.categoryDataSugarMillDao()?.allCategoryData
                    ?: ArrayList<SugarMillCategoryData>()) as ArrayList<SugarMillCategoryData>).forEach() {
                    arrayListCategory.add(it)
                }
                for ((index) in arrayListCategory.withIndex()) {
                    if (arrayListCategory[index].company_category
                            .equals(upComingPlannedId, ignoreCase = true)
                    ) {
                        categoryId = arrayListCategory[index].sugarmill_category_id.toString()
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
                        setSugarMillDropDown(categoryId, flEcn)
                    }
                }
            })
        } else {
            spinner_dd.text = "My Self"
            flEcn = "${preference?.getString("ecn")}"
            flName = "My Self"
        }
        setSugarMillDropDown(categoryId, null)
    }

    private fun setSugarMillDropDown(categoryId: String, flECN: String?) {
        val sugarMillStringList = ArrayList<String>()
        if (flECN == null) {
            sugarMillList.forEach() {
                if (categoryId.toInt() == (it.sugarmill_category_id ?: 0)) {
                    it.name?.let { it1 -> sugarMillStringList.add(it1) }
                }
            }
        } else {
            sugarMillCopyList.forEach() {
                if ((categoryId.toInt() == (it.sugarmill_category_id
                        ?: 0)) && flECN.equals(it.user_ecn, ignoreCase = true)
                ) {
                    it.name?.let { it1 -> sugarMillStringList.add(it1) }
                }
            }
        }

        selectSugarMill = NumberSpinner(this, sugarMillStringList, "Select Sugar Mill")
        selectSugarMill?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinner_estate_source.text = item
                if (flECN == null) {
                    for ((index) in sugarMillList.withIndex()) {
                        if (sugarMillList[index].name == item) {
                            sugarMillId = sugarMillList[index].sugarmill_id.toString()
                            break
                        }
                    }
                } else {
                    for ((index) in sugarMillCopyList.withIndex()) {
                        if (sugarMillCopyList[index].name == item) {
                            sugarMillId = sugarMillCopyList[index].sugarmill_id.toString()
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
        sugarMillAddAdapter =
            SugarMillAddAtpAdapter(arrayListAtpData, this@SugarMillPlanNewAtpActivity)
        rvListPlanAtpList.adapter = sugarMillAddAdapter
        sugarMillAddAdapter?.setLongClickListener(this)
    }

    override fun onLongClick(view: View, atpDbModel: SugarMillPlanAtpModel, position: Int) {
        if (arrayListAtpData.find { it.id == atpDbModel.id } != null) {
            arrayListAtpData.remove(arrayListAtpData.find { it.id == atpDbModel.id })
        }
        sugarMillAddAdapter?.notifyItemRemoved(position)
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
                appDatabase?.sugarMillPlanAtpModelDao()?.getAllAddPlanAtpModelData()?.forEach {
                    val time = DateTime(it.dateTime)
                    listOfSelectedDated.add(time)
                }
            }
        }
    }
}