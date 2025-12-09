package com.dhanuka.indhan.institutional.activity.influencers

import android.os.Bundle
import android.os.Handler
import android.os.Looper
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.ATPMenu.activity.OnClickListener
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.MenuScreenActivity.OfflineDatabase
import com.dhanuka.indhan.R
import com.dhanuka.indhan.institutional.adapter.influencers.SelfParticipationInfluencersOfflineAdapter
import com.dhanuka.indhan.institutional.model.RefreshEventModel
import com.dhanuka.indhan.institutional.model.influencers.InfluencersMarkArrivalDepartureAtpModel
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.CheckConnectionStatus
import kotlinx.android.synthetic.main.activity_tea_estate_offline_arrival_departure.*
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.GlobalScope
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import org.greenrobot.eventbus.EventBus

class OfflineArrivalDepartureInfluencers : BaseActivity() {

    private lateinit var appDatabase: AppDatabase
    private var arraySelfList = ArrayList<InfluencersMarkArrivalDepartureAtpModel>()
    private var adapter: SelfParticipationInfluencersOfflineAdapter? = null
    private lateinit var mActivity: OfflineArrivalDepartureInfluencers

    override fun onClick(v: View) {
        when (v.id) {
            R.id.ivBack -> {
                onBackPressed()
            }

            R.id.ivDownload -> {
                if (CheckConnectionStatus.isInternetAvailable(mActivity)) {
                    multipleFileUploadWebservice()
                } else {
                    makeToast(resources.getString(R.string.no_internet_connection))
                }
            }
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_tea_estate_offline_arrival_departure)
        mActivity = this
        appDatabase = DatabaseClient.getInstance(mActivity).appDatabase
        ivBack.setOnClickListener(this)
        ivDownload.setOnClickListener(this)
        getOfflineData()
    }

    private fun getOfflineData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val dataGot =
                    appDatabase.markArrivalDepartureAtpInfluencersDao()
                        .getAllArrivalDepartureListData()
                if (dataGot != null) {
                    if (dataGot.isNotEmpty()) {
                        arraySelfList =
                            dataGot as ArrayList<InfluencersMarkArrivalDepartureAtpModel>
                    }
                }
            }.run {
                withContext(Dispatchers.Main) {
                    if (arraySelfList.size > 0) {
                        txtNoTickets.visibility = View.GONE
                        recyclerViewActivate.visibility = View.VISIBLE
                        adapter =
                            SelfParticipationInfluencersOfflineAdapter(
                                arraySelfList,
                                mActivity,
                                object :
                                    OnClickListener {
                                    override fun onClick(position: Int) {
                                    }
                                })
                        recyclerViewActivate.layoutManager =
                            LinearLayoutManager(this@OfflineArrivalDepartureInfluencers)
                        recyclerViewActivate.adapter = adapter
                        adapter?.notifyDataSetChanged()
                    } else {
                        txtNoTickets.visibility = View.VISIBLE
                        recyclerViewActivate.visibility = View.GONE
                    }
                }
            }
        }

    }

    private fun multipleFileUploadWebservice() {
        val offlineDatabase = OfflineDatabase(mActivity)
        offlineDatabase.resetArrivalDepartureAtpInfluencersData()
        Handler(Looper.getMainLooper()).postDelayed({
            EventBus.getDefault().post(RefreshEventModel(data = 2, page = "Influencers_atp"))
            finish()
        }, 1000)
        makeToast("Your data will be uploaded in background. Please check after sometime.")
    }
}