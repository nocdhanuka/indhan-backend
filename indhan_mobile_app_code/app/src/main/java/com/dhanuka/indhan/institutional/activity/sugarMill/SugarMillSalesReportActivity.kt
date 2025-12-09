package com.dhanuka.indhan.institutional.activity.sugarMill

import android.os.Bundle
import android.text.TextUtils
import android.view.View
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.institutional.adapter.sugarMill.SugarMillSalesAdapter
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillData
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillSalesData
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillSalesResponse
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.roomdatabase.model.ApiTimeTableModel
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.OnSpinerItemClick
import kotlinx.android.synthetic.main.sugarmill_sales_report.*
import kotlinx.coroutines.*
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.text.SimpleDateFormat
import java.util.*
import kotlin.collections.ArrayList

class SugarMillSalesReportActivity : BaseActivity(), SugarMillSalesAdapter.OnClickItem,
    SwipeRefreshLayout.OnRefreshListener {
    private var sugarMillList: ArrayList<SugarMillData> = ArrayList()
    private var preference: Preference? = null
    private var appDatabase: AppDatabase? = null
    private var salesSugarMillAdapter: SugarMillSalesAdapter? = null
    private var salesListData: ArrayList<SugarMillSalesData> = ArrayList()
    private var year: String = ""
    private var month: String = ""
    private var sugarMillId: String = ""
    private var sugarMillNumberSpinner: NumberSpinner? = null
    private var yearSpinner: NumberSpinner? = null
    private var monthSpinner: NumberSpinner? = null

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.spinnerSugarMill -> {
                sugarMillNumberSpinner?.let {
                    if (!it.isEnable) {
                        it.showSpinerDialog()
                    }
                }
            }
            R.id.spinnerMonth -> {
                monthSpinner?.let {
                    if (!it.isEnable) {
                        it.showSpinerDialog()
                    }
                }
            }
            R.id.spinnerYear -> {
                yearSpinner?.let {
                    if (!it.isEnable) {
                        it.showSpinerDialog()
                    }
                }
            }
            R.id.btnReset -> {
                year = ""
                month = ""
                sugarMillId = ""
                spinnerSugarMill.text = ""
                spinnerMonth.text = ""
                spinnerYear.text = ""
                getLocalData(sugarMillId, month, year)
            }
            R.id.ivBack -> {
                finish()
            }
        }
    }

    override fun onCreate(savedInstancsugarMill: Bundle?) {
        super.onCreate(savedInstancsugarMill)
        setContentView(R.layout.sugarmill_sales_report)
        preference = Preference(this)
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        setOnClickListeners()
        setAdapter()
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            getSugarMillsSalesData(false)
        } else {
            getLocalData(sugarMillId, month, year)
        }
    }

    private fun setDropDownData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                sugarMillList.clear()
                (appDatabase?.sugarMillListDataDao()?.allSugarMill
                    ?: ArrayList<SugarMillData>()).forEach() {
                    sugarMillList.add(it!!)
                }
            }.run {
                val arrayListSugarMill = ArrayList<String>()
                sugarMillList.forEach() {
                    it.name?.let { it1 -> arrayListSugarMill.add(it1) }
                }
                sugarMillNumberSpinner = NumberSpinner(
                    this@SugarMillSalesReportActivity,
                    arrayListSugarMill,
                    "Select SugarMill"
                )
                sugarMillNumberSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
                    override fun onClick(item: String, position: Int) {
                        sugarMillId = sugarMillList[position].sugarmill_id.toString()
                        spinnerSugarMill.text = item
                        getLocalData(sugarMillId, month, year)
                    }
                })

                val arrayListMonth = ArrayList<String>()
                val arrayListYear = ArrayList<String>()
                salesListData.forEach() {
                    it.month?.let { it1 -> arrayListMonth.add(it1) }
                    it.year?.let { it1 -> arrayListYear.add(it1) }
                }
                arrayListMonth.distinct()
                arrayListYear.distinct()
                monthSpinner =
                    NumberSpinner(this@SugarMillSalesReportActivity, arrayListMonth, "Select Month")

                monthSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
                    override fun onClick(item: String, position: Int) {
                        month = item
                        spinnerMonth.text = "Month ".plus(item)
                        getLocalData(sugarMillId, month, year)
                    }
                })

                yearSpinner =
                    NumberSpinner(this@SugarMillSalesReportActivity, arrayListYear, "Select Year")
                yearSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
                    override fun onClick(item: String, position: Int) {
                        year = item
                        spinnerYear.text = "Year ".plus(item)
                        getLocalData(sugarMillId, month, year)
                    }
                })
            }
        }
    }

    private fun getLocalData(sugarMillId: String, month: String, year: String) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                if (TextUtils.isEmpty(sugarMillId) && TextUtils.isEmpty(month) && TextUtils.isEmpty(
                        year
                    )
                ) {
                    salesListData = (appDatabase?.sugarMillSalesDataDao()?.getAllSalesListData()
                        ?: ArrayList<SugarMillSalesData>()) as ArrayList<SugarMillSalesData>
                } else if (!TextUtils.isEmpty(sugarMillId) && !TextUtils.isEmpty(month) && !TextUtils.isEmpty(
                        year
                    )
                ) {
                    salesListData = (appDatabase?.sugarMillSalesDataDao()
                        ?.getAllSalesById(sugarMillId.toInt(), month, year)
                        ?: ArrayList<SugarMillSalesData>()) as ArrayList<SugarMillSalesData>
                } else if (TextUtils.isEmpty(month) && TextUtils.isEmpty(year)) {
                    salesListData =
                        (appDatabase?.sugarMillSalesDataDao()?.getAllSalesById(sugarMillId.toInt())
                            ?: ArrayList<SugarMillSalesData>()) as ArrayList<SugarMillSalesData>
                } else if (!TextUtils.isEmpty(month) && !TextUtils.isEmpty(year)) {
                    salesListData = (appDatabase?.sugarMillSalesDataDao()
                        ?.getAllSalesById(month, year)
                        ?: ArrayList<SugarMillSalesData>()) as ArrayList<SugarMillSalesData>
                }
            }.run {
                withContext(Dispatchers.Main) {
                    setDropDownData()
                    setAdapter()
                }
            }
        }
    }

    private fun getSugarMillsSalesData(b: Boolean) {
        swipeRefreshLayout.isRefreshing = false
        showProgress()
        val token = preference?.getString("token") ?: ""
        val query = HashMap<String, Any>()
        GlobalScope.launch {
            var timeData: ApiTimeTableModel? = null
            withContext(Dispatchers.IO) {
                timeData = "v3/sugarMillSalesReport".getLastHitTimeData()
            }.run {
                withContext(Dispatchers.Main) {
                    val time = "${timeData?.updatedAt ?: 0}"
                    query["apiToken"] = token
                    query["updated_date"] = if (b) "0" else time
                    RetrofitClient.mvpClient
                        .create(ApiServiceRequest::class.java).salesReportSugarMillList(query)
                        .subscribeOn(Schedulers.newThread())
                        .observeOn(AndroidSchedulers.mainThread())
                        .subscribe(object : Subscriber<SugarMillSalesResponse>() {
                            override fun onCompleted() {
                                hideProgress()
                            }

                            override fun onError(e: Throwable?) {
                                hideProgress()
                            }

                            override fun onNext(it: SugarMillSalesResponse?) {
                                if (it != null) {
                                    if (it.status_code == 200) {
                                        insertDataInDb(it.data)
                                    } else if (it.status_code == 409) {
                                        EventBus.getDefault().post("logout")
                                    }
                                }
                            }
                        })
                }
            }
        }
    }

    private fun insertDataInDb(data: List<SugarMillSalesData>?) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                data?.let {
                    it.forEach() { sugarMill ->
                        appDatabase?.sugarMillSalesDataDao()?.insert(sugarMill)
                    }
                }
                "v3/sugarMillSalesReport".updateTimeOnDb()
                delay(300)
            }.run {
                withContext(Dispatchers.Main) {
                    getLocalData(sugarMillId, month, year)
                    hideProgress()
                }
            }
        }
    }

    private fun String.getLastHitTimeData(): ApiTimeTableModel? {
        return appDatabase?.apiTimeDao()?.getApiTimeData(this)
    }

    private fun String.updateTimeOnDb() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val timeData = this@updateTimeOnDb.getLastHitTimeData()
                val sdf = SimpleDateFormat(Constants.DATE_TIME_FORMAT)
                val currentDate = sdf.format(Date())
                if (timeData != null) {
                    appDatabase?.apiTimeDao()?.updateTimeInDb(this@updateTimeOnDb, currentDate)
                } else {
                    val timeDataModel = ApiTimeTableModel()
                    timeDataModel.apiName = this@updateTimeOnDb
                    timeDataModel.updatedAt = currentDate
                    appDatabase?.apiTimeDao()?.insert(timeDataModel)
                }
            }
        }
    }

    private fun setOnClickListeners() {
        spinnerSugarMill.setOnClickListener(this)
        spinnerMonth.setOnClickListener(this)
        ivBack.setOnClickListener(this)
        spinnerYear.setOnClickListener(this)
        btnReset.setOnClickListener(this)
        swipeRefreshLayout.setOnRefreshListener(this)
    }

    private fun setAdapter() {
        salesSugarMillAdapter =
            SugarMillSalesAdapter(this@SugarMillSalesReportActivity, salesListData)
        rcvSalesList.adapter = salesSugarMillAdapter
        salesSugarMillAdapter?.setClickListener(this)
        if (salesListData.size > 0) {
            rcvSalesList.visibility = View.VISIBLE
            tvNoData.visibility = View.GONE
        } else {
            tvNoData.visibility = View.VISIBLE
            rcvSalesList.visibility = View.GONE
        }
    }

    override fun onClickView(view: View, subMenu: SugarMillSalesData) {

    }

    override fun onRefresh() {
        swipeRefreshLayout.isRefreshing = true
        getSugarMillsSalesData(true)
    }
}