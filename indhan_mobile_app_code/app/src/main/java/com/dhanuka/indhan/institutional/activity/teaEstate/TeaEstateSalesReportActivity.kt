package com.dhanuka.indhan.institutional.activity.teaEstate

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
import com.dhanuka.indhan.institutional.adapter.teaEstate.TeaEstateSalesAdapter
import com.dhanuka.indhan.institutional.model.TeaEstateSalesData
import com.dhanuka.indhan.institutional.model.TeaEstateSalesResponse
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstateStgData
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.roomdatabase.model.ApiTimeTableModel
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.OnSpinerItemClick
import kotlinx.android.synthetic.main.tea_estate_sales_report.*
import kotlinx.coroutines.*
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.text.SimpleDateFormat
import java.util.*
import kotlin.collections.ArrayList

class TeaEstateSalesReportActivity : BaseActivity(), TeaEstateSalesAdapter.OnClickItem,
    SwipeRefreshLayout.OnRefreshListener {
    private var teaEstateList: ArrayList<TeaEstateStgData> = ArrayList()
    private var preference: Preference? = null
    private var appDatabase: AppDatabase? = null
    private var salesTeaEstateAdapter: TeaEstateSalesAdapter? = null
    private var salesListData: ArrayList<TeaEstateSalesData> = ArrayList()
    private var year: String = ""
    private var month: String = ""
    private var teaEstateId: String = ""
    private var estateNumberSpinner: NumberSpinner? = null
    private var yearSpinner: NumberSpinner? = null
    private var monthSpinner: NumberSpinner? = null

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.spinnerTeaEstate -> {
                estateNumberSpinner?.let {
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
                teaEstateId = ""
                spinnerTeaEstate.text = ""
                spinnerMonth.text = ""
                spinnerYear.text = ""
                getLocalData(teaEstateId, month, year)
            }
            R.id.ivBack -> {
                finish()
            }
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.tea_estate_sales_report)
        preference = Preference(this)
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        setOnClickListeners()
        setAdapter()
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            getTeaEstatesSalesData(false)
        } else {
            getLocalData(teaEstateId, month, year)
        }
    }

    private fun setDropDownData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                teaEstateList.clear()
                (appDatabase?.teaEstateStgListDataDao()?.allTeaEstate
                    ?: ArrayList<TeaEstateStgData>()).forEach() {
                    teaEstateList.add(it!!)
                }
            }.run {
                val arrayListTeaEstate = ArrayList<String>()
                teaEstateList.forEach() {
                    it.name?.let { it1 -> arrayListTeaEstate.add(it1) }
                }
                estateNumberSpinner = NumberSpinner(
                    this@TeaEstateSalesReportActivity,
                    arrayListTeaEstate,
                    "Select TeaEstate/STG"
                )
                estateNumberSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
                    override fun onClick(item: String, position: Int) {
                        teaEstateId = teaEstateList[position].tea_estate_id.toString()
                        spinnerTeaEstate.text = item
                        getLocalData(teaEstateId, month, year)
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
                    NumberSpinner(this@TeaEstateSalesReportActivity, arrayListMonth, "Select Month")

                monthSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
                    override fun onClick(item: String, position: Int) {
                        month = item
                        spinnerMonth.text = "Month ".plus(item)
                        getLocalData(teaEstateId, month, year)
                    }
                })

                yearSpinner =
                    NumberSpinner(this@TeaEstateSalesReportActivity, arrayListYear, "Select Year")
                yearSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
                    override fun onClick(item: String, position: Int) {
                        year = item
                        spinnerYear.text = "Year ".plus(item)
                        getLocalData(teaEstateId, month, year)
                    }
                })
            }
        }
    }

    private fun getLocalData(teaEstateId: String, month: String, year: String) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                if (TextUtils.isEmpty(teaEstateId) && TextUtils.isEmpty(month) && TextUtils.isEmpty(
                        year
                    )
                ) {
                    salesListData = (appDatabase?.teaEstateSalesDataDao()?.getAllSalesListData()
                        ?: ArrayList<TeaEstateSalesData>()) as ArrayList<TeaEstateSalesData>
                } else if (!TextUtils.isEmpty(teaEstateId) && !TextUtils.isEmpty(month) && !TextUtils.isEmpty(
                        year
                    )
                ) {
                    salesListData = (appDatabase?.teaEstateSalesDataDao()
                        ?.getAllSalesById(teaEstateId.toInt(), month, year)
                        ?: ArrayList<TeaEstateSalesData>()) as ArrayList<TeaEstateSalesData>
                } else if (TextUtils.isEmpty(month) && TextUtils.isEmpty(year)) {
                    salesListData =
                        (appDatabase?.teaEstateSalesDataDao()?.getAllSalesById(teaEstateId.toInt())
                            ?: ArrayList<TeaEstateSalesData>()) as ArrayList<TeaEstateSalesData>
                } else if (!TextUtils.isEmpty(month) && !TextUtils.isEmpty(year)) {
                    salesListData = (appDatabase?.teaEstateSalesDataDao()
                        ?.getAllSalesById(month, year)
                        ?: ArrayList<TeaEstateSalesData>()) as ArrayList<TeaEstateSalesData>
                }
            }.run {
                withContext(Dispatchers.Main) {
                    setDropDownData()
                    setAdapter()
                }
            }
        }
    }

    private fun getTeaEstatesSalesData(b: Boolean) {
        swipeRefreshLayout.isRefreshing = false
        showProgress()
        val token = preference?.getString("token") ?: ""
        val query = HashMap<String, Any>()
        GlobalScope.launch {
            var timeData: ApiTimeTableModel? = null
            withContext(Dispatchers.IO) {
                timeData = "v3/teaEstateSalesReport".getLastHitTimeData()
            }.run {
                withContext(Dispatchers.Main) {
                    val time = "${timeData?.updatedAt ?: 0}"
                    query["apiToken"] = token
                    query["updated_date"] = if (b) "0" else time
                    RetrofitClient.mvpClient
                        .create(ApiServiceRequest::class.java).salesReportList(query)
                        .subscribeOn(Schedulers.newThread())
                        .observeOn(AndroidSchedulers.mainThread())
                        .subscribe(object : Subscriber<TeaEstateSalesResponse>() {
                            override fun onCompleted() {
                                hideProgress()
                            }

                            override fun onError(e: Throwable?) {
                                hideProgress()
                            }

                            override fun onNext(it: TeaEstateSalesResponse?) {
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

    private fun insertDataInDb(data: List<TeaEstateSalesData>?) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                data?.let {
                    it.forEach() { teaEstate ->
                        appDatabase?.teaEstateSalesDataDao()?.insert(teaEstate)
                    }
                }
                "v3/teaEstateSalesReport".updateTimeOnDb()
                delay(300)
            }.run {
                withContext(Dispatchers.Main) {
                    getLocalData(teaEstateId, month, year)
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
        spinnerTeaEstate.setOnClickListener(this)
        spinnerMonth.setOnClickListener(this)
        ivBack.setOnClickListener(this)
        spinnerYear.setOnClickListener(this)
        btnReset.setOnClickListener(this)
        swipeRefreshLayout.setOnRefreshListener(this)
    }

    private fun setAdapter() {
        salesTeaEstateAdapter =
            TeaEstateSalesAdapter(this@TeaEstateSalesReportActivity, salesListData)
        rcvSalesList.adapter = salesTeaEstateAdapter
        salesTeaEstateAdapter?.setClickListener(this)
        if (salesListData.size > 0) {
            rcvSalesList.visibility = View.VISIBLE
            tvNoData.visibility = View.GONE
        } else {
            tvNoData.visibility = View.VISIBLE
            rcvSalesList.visibility = View.GONE
        }
    }

    override fun onClickView(view: View, subMenu: TeaEstateSalesData) {

    }

    override fun onRefresh() {
        swipeRefreshLayout.isRefreshing = true
        getTeaEstatesSalesData(true)
    }
}