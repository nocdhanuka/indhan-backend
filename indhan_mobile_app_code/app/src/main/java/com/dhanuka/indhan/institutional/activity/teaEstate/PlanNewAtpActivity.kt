package com.dhanuka.indhan.institutional.activity.teaEstate

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
import com.dhanuka.indhan.institutional.adapter.teaEstate.TeaEstateAddAtpAdapter
import com.dhanuka.indhan.institutional.model.CategoryData
import com.dhanuka.indhan.institutional.model.FlDataList
import com.dhanuka.indhan.institutional.model.RefreshEventModel
import com.dhanuka.indhan.institutional.model.teaEstate.AtpData
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstatePlanAtpApiModel
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstatePlanAtpModel
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstateStgData
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.plan_new_atp_activity.*
import kotlinx.coroutines.*
import noman.weekcalendar.listener.OnDateClickListener
import org.greenrobot.eventbus.EventBus
import org.joda.time.DateTime
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*
import kotlin.math.pow

class PlanNewAtpActivity : BaseActivity(), TeaEstateAddAtpAdapter.OrClickListener,
    OnDateClickListener {

    private var flEcn: String = ""
    private var flName: String = ""
    private var teaEstateId: String = ""
    private var categoryId: String = "0"
    private var ticketId: String = ""
    private var date = ""

    val listOfSelectedDated: ArrayList<DateTime> = ArrayList()
    private var teaEstateAddAdapter: TeaEstateAddAtpAdapter? = null
    private var arrayListAtpData: ArrayList<TeaEstatePlanAtpModel> = ArrayList()
    private var arrayListCategory: ArrayList<CategoryData> = ArrayList()

    private var upComingPlannedId = ""
    private var flDataList: ArrayList<FlDataList> = ArrayList()
    private var teaEstateList: ArrayList<TeaEstateStgData> = ArrayList()
    private var teaEstateCopyList: ArrayList<TeaEstateStgData> = ArrayList()
    private var preference: Preference? = null
    private var userId = ""
    private var userType = ""
    private var appDatabase: AppDatabase? = null
    private var selectTeaEstate: NumberSpinner? = null
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
                selectTeaEstate?.let {
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
        var data: TeaEstatePlanAtpApiModel? = null
        val atpDataList: ArrayList<AtpData> = ArrayList()
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                appDatabase?.teaEstatePlanAtpModelDao()?.getAllAddPlanAtpModelData()?.forEach {
                    val atpData = AtpData(
                        date_time = it.dateTime,
                        estate_category = it.companyCategory,
                        fl_ecn = it.flEcn,
                        fl_name = it.flName,
                        fl_type = it.flType,
                        is_manager = it.manager,
                        tea_estate_id = it.teaEstateId,
                        ticket_number = it.ticketNumber
                    )
                    atpDataList.add(atpData)
                }
                delay(300)
                data =
                    TeaEstatePlanAtpApiModel(
                        apiToken = preference?.getString("token") ?: "",
                        atp_data = atpDataList
                    )

            }.run {
                withContext(Dispatchers.Main) {
                    if (data != null) {
                        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
                            .planAtpTeaEstateSubmit(data)
                            .subscribeOn(Schedulers.newThread())
                            .observeOn(AndroidSchedulers.mainThread())
                            .subscribe(object : Subscriber<CommonAtpResponse>() {
                                override fun onCompleted() {
                                    submit.isEnabled = true
                                    hideProgress()
                                }

                                override fun onError(e: Throwable) {
                                    submit.isEnabled = true
                                    hideProgress()
                                    makeToast("Failure")
                                    updateCalenderWithSelectedTime(false)
                                    upDateRcView()
                                    restoreDefault()
                                    calendar.setStartDate(
                                        DateTime().plusDays(0),
                                        listOfSelectedDated
                                    )
                                }

                                override fun onNext(homeBean: CommonAtpResponse?) {
                                    submit.isEnabled = true
                                    hideProgress()
                                    if (homeBean?.status_code == 200) {
                                        GlobalScope.launch {
                                            withContext(Dispatchers.IO) {
                                                appDatabase?.teaEstatePlanAtpModelDao()
                                                    ?.deleteAllPlanAtpModelData()
                                            }
                                        }
                                        hideProgress()
                                        makeToast((homeBean.message).plus(", Please refresh this screen."))
                                        EventBus.getDefault()
                                            .post(
                                                RefreshEventModel(
                                                    data = 2,
                                                    page = "tea_estate_atp"
                                                )
                                            )
                                        finish()
                                    } else {
                                        submit.isEnabled = true
                                        hideProgress()
                                        makeToast(homeBean?.message ?: "error")
                                    }
                                }
                            })
                    } else {
                        submit.isEnabled = true
                        hideProgress()
                        makeToast("No Atp data to plan.")
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
                    appDatabase?.teaEstatePlanAtpModelDao()?.getAllAddPlanAtpModelData(0)
                        ?: ArrayList<TeaEstatePlanAtpModel>()
                )
            }
        }
        teaEstateAddAdapter?.notifyItemChanged(arrayListAtpData.size)
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
        teaEstateId = ""
    }

    private fun saveAtp() {
        val resp = checkValues()
        if (resp.equals("perfect", ignoreCase = true)) {
            val model = TeaEstatePlanAtpModel()
            ticketId = "PV3${randomInt(2)}${randomInt(2)}${randomInt(2)}${randomInt(1)}"
            model.apiToken = preference?.getString("token") ?: ""
            model.status = 0
            model.hitTime = System.currentTimeMillis()
            model.dateTime = date
            model.flType = "bdo"
            model.flEcn = flEcn
            model.flName = flName
            model.ticketNumber = ticketId
            model.teaEstateId = teaEstateId
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
            teaEstateAddAdapter?.notifyItemChanged(arrayListAtpData.size)
            saveDataLocallyAndClear(model)
            calendar.setStartDate(DateTime().plusDays(0), listOfSelectedDated)
        } else {
            makeToast(resp)
        }
    }

    private fun saveDataLocallyAndClear(model: TeaEstatePlanAtpModel) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                appDatabase?.teaEstatePlanAtpModelDao()?.insert(model)
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
            TextUtils.isEmpty(teaEstateId) -> {
                "Please Select TeaEstate/STG/Branch for atp plan."
            }
            else -> "perfect"
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.plan_new_atp_activity)
        updateCalenderWithSelectedTime(true)
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
                    (appDatabase?.flDataListDao()?.getAllFlByManagerEcn(userId)
                        ?: ArrayList<FlDataList>()).forEach() {
                        flDataList.add(it!!)
                    }
                }

                teaEstateList.clear()
                (appDatabase?.teaEstateStgListDataDao()?.allTeaEstate
                    ?: ArrayList<TeaEstateStgData>()).forEach() {
                    teaEstateList.add(it!!)
                    teaEstateCopyList.add(it)
                }
                ((appDatabase?.categoryDataDao()?.allCategoryData
                    ?: ArrayList<CategoryData>()) as ArrayList<CategoryData>).forEach() {
                    arrayListCategory.add(it)
                }
                for ((index) in arrayListCategory.withIndex()) {
                    if (arrayListCategory[index].company_category.toString()
                            .equals(upComingPlannedId, ignoreCase = true)
                    ) {
                        categoryId = arrayListCategory[index].estate_category_id.toString()
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
                it.name?.let { it1 -> flList.add(it1) }
            }
            selectFl = NumberSpinner(this, flList, "Select FL")
            selectFl?.bindOnSpinerListener(object : OnSpinerItemClick {
                override fun onClick(item: String, position: Int) {
                    if (flDataList.size > 0) {
                        spinner_dd.text = item
                        flEcn = flDataList[position].ECN ?: ""
                        flName = flDataList[position].name ?: ""
                        setTeaEstateDropDown(categoryId, flEcn)
                    }
                }
            })
        } else {
            spinner_dd.text = "My Self"
            flEcn = "${preference?.getString("ecn")}"
            flName = "My Self"
        }
        setTeaEstateDropDown(categoryId, null)
    }

    private fun setTeaEstateDropDown(categoryId: String, flECN: String?) {
        val teaEstateStringList = ArrayList<String>()
        if (flECN == null) {
            teaEstateList.forEach() {
                if (categoryId.toInt() == (it.estate_category_id ?: 0)) {
                    it.name?.let { it1 -> teaEstateStringList.add(it1) }
                }
            }
        } else {
            teaEstateCopyList.forEach() {
                if ((categoryId.toInt() == (it.estate_category_id
                        ?: 0)) && flECN.equals(it.user_ecn, ignoreCase = true)
                ) {
                    it.name?.let { it1 -> teaEstateStringList.add(it1) }
                }
            }
        }
        selectTeaEstate = NumberSpinner(this, teaEstateStringList, "Select Tea Estate")
        selectTeaEstate?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinner_estate_source.text = item
                if (flECN == null) {
                    for ((index) in teaEstateList.withIndex()) {
                        if (teaEstateList[index].name == item) {
                            teaEstateId = teaEstateList[index].tea_estate_id.toString()
                            break
                        }
                    }
                } else {
                    for ((index) in teaEstateCopyList.withIndex()) {
                        if (teaEstateCopyList[index].name == item) {
                            teaEstateId = teaEstateCopyList[index].tea_estate_id.toString()
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
        teaEstateAddAdapter = TeaEstateAddAtpAdapter(arrayListAtpData, this@PlanNewAtpActivity)
        rvListPlanAtpList.adapter = teaEstateAddAdapter
        teaEstateAddAdapter?.setLongClickListener(this)
    }

    override fun onLongClick(view: View, atpDbModel: TeaEstatePlanAtpModel, position: Int) {
        if (arrayListAtpData.find { it.id == atpDbModel.id } != null) {
            arrayListAtpData.remove(arrayListAtpData.find { it.id == atpDbModel.id })
        }
        teaEstateAddAdapter?.notifyItemRemoved(position)
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
            if (dateTime.plusHours(1) < DateTime().minusDays(0)) {
                calendar.reset()
                date = DateTime().plusDays(0).toString();
                makeToast("You can not plan for past dates")
                Handler(Looper.getMainLooper()).postDelayed({
                    updateCalenderWithSelectedTime(false)
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
//                calendar.reset()
//                date = DateTime().plusDays(0).toString();
//                makeToast("You can not plan for current date")
//                Handler(Looper.getMainLooper()).postDelayed({
//                    updateCalenderWithSelectedTime(false)
//                    calendar.setStartDate(DateTime().plusDays(0), listOfSelectedDated)
//                    calendar.setSelectedDate(DateTime().plusDays(0))
//                    (DateTime().plusDays(0).dayOfMonth()
//                        .getAsText(Locale.ENGLISH) + " " + DateTime().monthOfYear()
//                        .getAsText(Locale.ENGLISH) + ", " + DateTime().year()
//                        .getAsText(Locale.ENGLISH)).also { tvMonth.text = it }
//                }, 300)
//                return
//        }
        if (!(dateTime?.monthOfYear() == null || dateTime.year() == null)) {
            (dateTime.dayOfMonth().getAsText(Locale.ENGLISH) + " " + dateTime.monthOfYear()
                .getAsText(Locale.ENGLISH) + ", " + dateTime.year()
                .getAsText(Locale.ENGLISH)).also { tvMonth.text = it }

            date = dateTime.toString()
        }
    }

    private fun updateCalenderWithSelectedTime(b: Boolean) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                listOfSelectedDated.clear()
                appDatabase?.teaEstatePlanAtpModelDao()?.getAllAddPlanAtpModelData()?.forEach {
                    val time = DateTime(it.dateTime)
                    listOfSelectedDated.add(time)
                    if (b) {
                        arrayListAtpData.add(it)
                    }
                }
            }
        }
    }
}