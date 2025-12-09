package com.dhanuka.indhan.institutional.activity.influencers

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
import com.dhanuka.indhan.institutional.adapter.influencers.InfluencersSalesAdapter
import com.dhanuka.indhan.institutional.model.influencers.InfluencersData
import com.dhanuka.indhan.institutional.model.influencers.InfluencersSalesData
import com.dhanuka.indhan.institutional.model.influencers.InfluencersSalesResponse
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.roomdatabase.model.ApiTimeTableModel
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.OnSpinerItemClick
import kotlinx.android.synthetic.main.influencers_sales_report.*
import kotlinx.coroutines.*
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.text.SimpleDateFormat
import java.util.*
import kotlin.collections.ArrayList

class InfluencersSalesReportActivity : BaseActivity(), InfluencersSalesAdapter.OnClickItem,
    SwipeRefreshLayout.OnRefreshListener {
    private var InfluencersList: ArrayList<InfluencersData> = ArrayList()
    private var preference: Preference? = null
    private var appDatabase: AppDatabase? = null
    private var salesInfluencersAdapter: InfluencersSalesAdapter? = null
    private var salesListData: ArrayList<InfluencersSalesData> = ArrayList()
    private var year: String = ""
    private var month: String = ""
    private var InfluencersId: String = ""
    private var InfluencersNumberSpinner: NumberSpinner? = null
    private var yearSpinner: NumberSpinner? = null
    private var monthSpinner: NumberSpinner? = null

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.spinnerInfluencer -> {
                InfluencersNumberSpinner?.let {
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
                InfluencersId = ""
                spinnerInfluencer.text = ""
                spinnerMonth.text = ""
                spinnerYear.text = ""
                getLocalData(InfluencersId, month, year)
            }
            R.id.ivBack -> {
                finish()
            }
        }
    }

    override fun onCreate(savedInstancInfluencers: Bundle?) {
        super.onCreate(savedInstancInfluencers)
        setContentView(R.layout.influencers_sales_report)
        preference = Preference(this)
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        setOnClickListeners()
        setAdapter()
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            getInfluencerssSalesData(false)
        } else {
            getLocalData(InfluencersId, month, year)
        }
    }

    private fun setDropDownData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                InfluencersList.clear()
                (appDatabase?.influencersListDataDao()?.allInfluencers
                    ?: ArrayList<InfluencersData>()).forEach() {
                    InfluencersList.add(it!!)
                }
            }.run {
                val arrayListInfluencers = ArrayList<String>()
                InfluencersList.forEach() {
                    it.name?.let { it1 -> arrayListInfluencers.add(it1) }
                }
                InfluencersNumberSpinner = NumberSpinner(
                    this@InfluencersSalesReportActivity,
                    arrayListInfluencers,
                    "Select Influencers"
                )
                InfluencersNumberSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
                    override fun onClick(item: String, position: Int) {
                        InfluencersId = InfluencersList[position].influencer_id.toString()
                        spinnerInfluencer.text = item
                        getLocalData(InfluencersId, month, year)
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
                    NumberSpinner(
                        this@InfluencersSalesReportActivity,
                        arrayListMonth,
                        "Select Month"
                    )

                monthSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
                    override fun onClick(item: String, position: Int) {
                        month = item
                        spinnerMonth.text = "Month ".plus(item)
                        getLocalData(InfluencersId, month, year)
                    }
                })

                yearSpinner =
                    NumberSpinner(this@InfluencersSalesReportActivity, arrayListYear, "Select Year")
                yearSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
                    override fun onClick(item: String, position: Int) {
                        year = item
                        spinnerYear.text = "Year ".plus(item)
                        getLocalData(InfluencersId, month, year)
                    }
                })
            }
        }
    }

    private fun getLocalData(InfluencersId: String, month: String, year: String) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                if (TextUtils.isEmpty(InfluencersId) && TextUtils.isEmpty(month) && TextUtils.isEmpty(
                        year
                    )
                ) {
                    salesListData = (appDatabase?.influencersSalesDataDao()?.getAllSalesListData()
                        ?: ArrayList<InfluencersSalesData>()) as ArrayList<InfluencersSalesData>
                } else if (!TextUtils.isEmpty(InfluencersId) && !TextUtils.isEmpty(month) && !TextUtils.isEmpty(
                        year
                    )
                ) {
                    salesListData = (appDatabase?.influencersSalesDataDao()
                        ?.getAllSalesById(InfluencersId.toInt(), month, year)
                        ?: ArrayList<InfluencersSalesData>()) as ArrayList<InfluencersSalesData>
                } else if (TextUtils.isEmpty(month) && TextUtils.isEmpty(year)) {
                    salesListData =
                        (appDatabase?.influencersSalesDataDao()
                            ?.getAllSalesById(InfluencersId.toInt())
                            ?: ArrayList<InfluencersSalesData>()) as ArrayList<InfluencersSalesData>
                } else if (!TextUtils.isEmpty(month) && !TextUtils.isEmpty(year)) {
                    salesListData = (appDatabase?.influencersSalesDataDao()
                        ?.getAllSalesById(month, year)
                        ?: ArrayList<InfluencersSalesData>()) as ArrayList<InfluencersSalesData>
                }
            }.run {
                withContext(Dispatchers.Main) {
                    setDropDownData()
                    setAdapter()
                }
            }
        }
    }

    private fun getInfluencerssSalesData(b: Boolean) {
        swipeRefreshLayout.isRefreshing = false
        showProgress()
        val token = preference?.getString("token") ?: ""
        val query = HashMap<String, Any>()
        GlobalScope.launch {
            var timeData: ApiTimeTableModel? = null
            withContext(Dispatchers.IO) {
                timeData = "v3/InfluencersSalesReport".getLastHitTimeData()
            }.run {
                withContext(Dispatchers.Main) {
                    val time = "${timeData?.updatedAt ?: 0}"
                    query["apiToken"] = token
                    query["updated_date"] = if (b) "0" else time
                    RetrofitClient.mvpClient
                        .create(ApiServiceRequest::class.java).salesReportInfluencersList(query)
                        .subscribeOn(Schedulers.newThread())
                        .observeOn(AndroidSchedulers.mainThread())
                        .subscribe(object : Subscriber<InfluencersSalesResponse>() {
                            override fun onCompleted() {
                                hideProgress()
                            }

                            override fun onError(e: Throwable?) {
                                hideProgress()
                            }

                            override fun onNext(it: InfluencersSalesResponse?) {
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

    private fun insertDataInDb(data: List<InfluencersSalesData>?) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                data?.let {
                    it.forEach() { Influencers ->
                        appDatabase?.influencersSalesDataDao()?.insert(Influencers)
                    }
                }
                "v3/InfluencersSalesReport".updateTimeOnDb()
                delay(300)
            }.run {
                withContext(Dispatchers.Main) {
                    getLocalData(InfluencersId, month, year)
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
        spinnerInfluencer.setOnClickListener(this)
        spinnerMonth.setOnClickListener(this)
        ivBack.setOnClickListener(this)
        spinnerYear.setOnClickListener(this)
        btnReset.setOnClickListener(this)
        swipeRefreshLayout.setOnRefreshListener(this)
    }

    private fun setAdapter() {
        salesInfluencersAdapter =
            InfluencersSalesAdapter(this@InfluencersSalesReportActivity, salesListData)
        rcvSalesList.adapter = salesInfluencersAdapter
        salesInfluencersAdapter?.setClickListener(this)
        if (salesListData.size > 0) {
            rcvSalesList.visibility = View.VISIBLE
            tvNoData.visibility = View.GONE
        } else {
            tvNoData.visibility = View.VISIBLE
            rcvSalesList.visibility = View.GONE
        }
    }

    override fun onClickView(view: View, subMenu: InfluencersSalesData) {

    }

    override fun onRefresh() {
        swipeRefreshLayout.isRefreshing = true
        getInfluencerssSalesData(true)
    }
}