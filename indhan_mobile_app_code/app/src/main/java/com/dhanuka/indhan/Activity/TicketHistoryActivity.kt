package com.dhanuka.indhan.Activity

import android.content.Intent
import android.os.Bundle
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout
import com.dhanuka.indhan.ATPMenu.activity.supervisorAtp.fragment.PaginationListener
import com.dhanuka.indhan.Activity.TicketHistoryMVP.Model.TicketHistoryModel
import com.dhanuka.indhan.Activity.TicketHistoryMVP.Model.TicketHistoryModel.Datum
import com.dhanuka.indhan.Activity.TicketHistoryMVP.TicketHistoryAdapter
import com.dhanuka.indhan.Activity.TicketHistoryMVP.TicketHistoryClickListener
import com.dhanuka.indhan.Activity.TicketHistoryMVP.TicketHistoryContracts
import com.dhanuka.indhan.Activity.TicketHistoryMVP.TicketHistoryPresenterImpl
import com.dhanuka.indhan.Activity.offlineClaimTicket.ManualTicketClaim
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.roomdatabase.model.ApiTimeTableModel
import com.dhanuka.indhan.util.CheckConnectionStatus
import kotlinx.android.synthetic.main.activity_ticket_history.*
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.GlobalScope
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import org.greenrobot.eventbus.EventBus
import rx.Single
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.text.SimpleDateFormat
import java.util.*

/**
 * Created by JOhn on 2/28/2018.
 */

class TicketHistoryActivity : BaseActivity(), TicketHistoryClickListener,
    TicketHistoryContracts.ITicketHistoryView, SwipeRefreshLayout.OnRefreshListener {
    private lateinit var mAdapter: TicketHistoryAdapter
    private val ticketRecordResponseModels = ArrayList<Datum>()
    private var Token = ""
    private lateinit var ticketHistoryPresenter: TicketHistoryContracts.TicketHistoryPresenter
    var from = ""
    var farmerId = ""
    var demo_request_id = ""
    var contactNumber = ""
    private var currentPage = PaginationListener.PAGE_START
    private var mIsLastPage = false
    private var mIsLoading = false
    private var mFromPullToRefresh = false
    private lateinit var linearLayout: LinearLayoutManager
    private var totalPage = 50
    private lateinit var databaseClient: AppDatabase
    private var lastSyncedTicketData = "0"

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_ticket_history)
        Token = Preference.getInstance(this).getString("token")
        databaseClient = DatabaseClient.getInstance(this).appDatabase
        getLastHitTimeData("v3/ticketHistory/{api_token}/{ticket_number}")
        ivBack.setOnClickListener(this)
        val userType = Preference.getInstance(this).getString("user_type")
        if (!userType.equals("dhanuka_doctor", false)) {
            flvOfflineTicketClaim.visibility = View.VISIBLE
        } else {
            flvOfflineTicketClaim.visibility = View.GONE
        }
        flvOfflineTicketClaim.setOnClickListener(this)
        swipeRefreshLayout.setOnRefreshListener(this)
        ticketHistoryPresenter = TicketHistoryPresenterImpl(this)
        setRecyclerAdapter()
        getTicketHistoryData()
    }

    private fun setRecyclerAdapter() {
        mAdapter = TicketHistoryAdapter(ticketRecordResponseModels, this)
        mAdapter.setClickListener(this)
        linearLayout = LinearLayoutManager(this)
        rvList.layoutManager = linearLayout
        rvList.adapter = mAdapter
    }

    private fun getServerData(isProgress: Boolean) {
        if (CheckConnectionStatus.isInternetAvailable(this@TicketHistoryActivity)) {
            ticketHistoryPresenter.onSubmitClick(
                Token,
                lastSyncedTicketData,
                "0",
                isProgress
            )
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }
    }

    override fun onClick(v: View) {
        when (v.id) {
            R.id.ivBack -> {
                onBackPressed()
            }
            R.id.flvOfflineTicketClaim -> {
                onOfflineButtonClick()
            }
            R.id.swipeRefreshLayout -> swipeRefreshLayout.post {
                swipeRefreshLayout.isRefreshing = true
                if (from == "acpfarmer") {
                    ticketRecordResponseModels.clear()
                    callBdoHistory(farmerId, contactNumber)
                } else {
                    getServerData(false)
                }
            }
        }
    }

    private fun onOfflineButtonClick() {
        val intent = Intent(this, ManualTicketClaim::class.java)
        intent.putExtra("from_ticket", true)
        startActivity(intent)
    }

    override fun hideProgress() {
        dismissProgress()
    }

    override fun setItems(items: TicketHistoryModel) {
        if (items.statusCode == 200) {
            swipeRefreshLayout.isRefreshing = false
            if (items.data?.isNullOrEmpty() == false) {
                "v3/ticketHistory/{api_token}/{ticket_number}".updateTimeOnDb()
                items.data?.let { insertTicketHistoryInDB(it) }
            }
        } else if (items.statusCode == 409) {
            val preference: Preference = Preference.getInstance(this)
            preference.putString("ticketId", "")
            preference.putString("ticketTime", "")
            preference.putBolean("isLogin", false)
            preference.putString("token", "")
            launchIntent(LoginActivity::class.java, false)
            finishAffinity()
        } else {
            makeToast(items.message)
        }
    }

    override fun onClick(view: View, ticketHistoryBean: Datum) {
        val extras = Bundle()
        extras.putSerializable("ticket", ticketHistoryBean)
        val intent = Intent(this, TicketDetailActivity::class.java)
        intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
        intent.putExtras(extras)
        startActivityForResult(intent, 220)
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (resultCode == RESULT_OK) {
            if (requestCode == 220) {
                getLastHitTimeData("v3/ticketHistory/{api_token}/{ticket_number}")
                if (from == "acpfarmer") {
                    ticketRecordResponseModels.clear()
                    callBdoHistory(farmerId, contactNumber)
                } else {
                    getServerData(true)
                }
            }
        }
    }

    override fun onRefresh() {
        lastSyncedTicketData = "0"
        mFromPullToRefresh = true
        swipeRefreshLayout.isRefreshing = true
        if (from == "acpfarmer") {
            ticketRecordResponseModels.clear()
            callBdoHistory(farmerId, contactNumber)
        } else {
            getServerData(true)
        }
    }

    /**
     * function for getting BDo activity details
     */
    private fun callBdoHistory(farmerId: String, contactNumber: String) {
        showProgress()
        val query = HashMap<String, Any>()
        query["apiToken"] = Token
        query["farmer_id"] = farmerId
        query["contact_number"] = contactNumber
        query["demo_request_id"] = demo_request_id
        query["offset"] = "0"

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).bdoFarmerList(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<TicketHistoryModel>() {
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

                override fun onNext(listBean: TicketHistoryModel) {
                    hideProgress()
                    if (listBean.statusCode == 200) {
                        if ((listBean.data?.size ?: 0) > 0) {
                            ticketRecordResponseModels.addAll(listBean.data!!)
                            if (currentPage < totalPage) {
                            } else {
                                mIsLastPage = true
                            }
                            mIsLoading = false
                        }
                        if (ticketRecordResponseModels.size > 0) {
                            txtEmpty.visibility = View.GONE
                            mAdapter.notifyDataSetChanged()

                        } else {
                            txtEmpty.visibility = View.VISIBLE
                            mAdapter.notifyDataSetChanged()
                        }
                    } else if (listBean.statusCode == 409) {
                        EventBus.getDefault().post("logout")
                    }
                }
            })
    }

    private fun insertTicketHistoryInDB(data: List<Datum>) {
        Single.fromCallable {
            for (i in data.indices)
                databaseClient.ticketHistoryDao()?.insert(data[i])
            getLastHitTimeData("v3/ticketHistory/{api_token}/{ticket_number}")
            getLocalTicketHistory()
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun getLocalTicketHistory() {
        val list = ArrayList<Datum>()
        Single.fromCallable {
            list.addAll(databaseClient.ticketHistoryDao().allTicketHistory as ArrayList<Datum>)
            val simpleDateFormat = SimpleDateFormat("EEEE, MMMM dd yyyy, hh:mm a", Locale.ENGLISH)
            list.sortWith(Comparator { datum: Datum, datum1: Datum ->
                return@Comparator simpleDateFormat.parse(datum1.datetimeFormat)
                    .compareTo(simpleDateFormat.parse(datum.datetimeFormat))
            })
            runOnUiThread() {
                if (list.size > 0) {
                    setAdapter(list)
                }
            }
        }.subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread()).subscribe()
    }

    private fun setAdapter(items: List<Datum>) {
        ticketRecordResponseModels.clear()
        ticketRecordResponseModels.addAll(items)
        mAdapter.notifyDataSetChanged()

        if (CheckConnectionStatus.isInternetAvailable(this@TicketHistoryActivity)) {
            GlobalScope.launch {
                withContext(Dispatchers.IO) {
                    val listOfDbData =
                        databaseClient.manualTicketClaimDao().getAllManualTicketClaimData()
                    if (listOfDbData != null) {
                        if (listOfDbData.isNotEmpty()) {
                            items.forEach() { ticketData ->
                                listOfDbData.forEach() {
                                    if (ticketData.ticket.trim().equals(
                                            it.ticket_number.trim(),
                                            ignoreCase = true
                                        )
                                    ) {
                                        val currentHitTimeTwoHour = System.currentTimeMillis()
                                        databaseClient.manualTicketClaimDao()
                                            .updateManualTicketClaimData(
                                                it.id,
                                                0,
                                                it.ticket_number,
                                                currentHitTimeTwoHour
                                            )
                                    }
                                }
                            }
                        }
                    }

                }
            }

        }
    }

    private fun String.updateTimeOnDb() {
        Single.fromCallable {
            val timeData = getLastHitTimeData(this)
            val sdf = SimpleDateFormat(Constants.DATE_TIME_FORMAT)
            val currentDate = sdf.format(Date())
            if (timeData != null) {
                databaseClient.apiTimeDao().updateTimeInDb(this, currentDate)
            } else {
                val timeDataModel = ApiTimeTableModel()
                timeDataModel.apiName = this
                timeDataModel.updatedAt = currentDate
                databaseClient.apiTimeDao().insert(timeDataModel)
            }
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun getLastHitTimeData(apiName: String) {
        Single.fromCallable() {
            val data = databaseClient.apiTimeDao().getApiTimeData(apiName)
            lastSyncedTicketData = data?.updatedAt ?: "0"
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun getTicketHistoryData() {
        ticketRecordResponseModels.clear()
        intent?.let {
            if (it.hasExtra(Constants.FARMER_FROM)) {
                from = it.getStringExtra(Constants.FARMER_FROM) ?: ""
            }
            if (it.hasExtra("farmerId")) {
                farmerId = it.getStringExtra("farmerId") ?: ""
            }
            if (it.hasExtra("contactNumber")) {
                contactNumber = it.getStringExtra("contactNumber") ?: ""
            }
            if (it.hasExtra("demo_request_id")) {
                demo_request_id = it.getStringExtra("demo_request_id") ?: ""
            }
        }
        if (from == "acpfarmer") {
            callBdoHistory(farmerId, contactNumber)
        } else {
            swipeRefreshLayout.isRefreshing = false
            getServerData(false)
            getLocalTicketHistory()
        }
    }
}
