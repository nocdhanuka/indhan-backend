package com.dhanuka.indhan.Activity.offlineClaimTicket

import android.os.Bundle
import android.os.Handler
import android.os.Looper
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.ATPMenu.activity.OnClickListener
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.MenuScreenActivity.OfflineDatabase
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.CheckConnectionStatus
import kotlinx.android.synthetic.main.activity_self_participation2.*
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.GlobalScope
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import java.io.File

class ManualTicketDetailsOfflineActivity : BaseActivity(), View.OnClickListener {
    private lateinit var adapter: ManualTicketOfflineAdapter
    private lateinit var adapter2: ManualSelfParticipationAdapter
    private lateinit var mActivity: ManualTicketDetailsOfflineActivity
    private var imageOfflineModel = ArrayList<ManualTicketClaimImage>()
    lateinit var model: ManualTicketClaimImage
    private lateinit var appDatabase: AppDatabase
    var imgfile: File? = null
    var imgUrl = ""
    var token = ""
    private var fromScreen = false
    lateinit var preference: Preference

    override fun onClick(v: View) {
        when (v.id) {
            R.id.ivBack -> {
                onBackPressed()
            }
            R.id.ivDownload -> {
                if (CheckConnectionStatus.isInternetAvailable(mActivity)) {
                    invalidateData()
                } else {
                    makeToast(resources.getString(R.string.no_internet_connection))
                }
            }
        }
    }

    private fun invalidateData() {
        if (imageOfflineModel.size > 0) {
            uploadTicketImage()
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_ticket_details_offline)
        mActivity = this
        preference = Preference(mActivity)
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        token = preference.getString("token")
        fromScreen = intent?.getBooleanExtra("from_ticket", false) ?: false
        ivBack.setOnClickListener(this)
        ivDownload.setOnClickListener(this)
        getOfflineData()
    }

    private fun getOfflineData() {
        GlobalScope.launch {
            val type = if (fromScreen) 1 else 2
            withContext(Dispatchers.IO) {
                imageOfflineModel =
                    if (type == 1) (appDatabase.manualTicketClaimDao()
                        .getAllManualTicketClaimData(type)
                        ?: ArrayList()) as ArrayList<ManualTicketClaimImage>
                    else (appDatabase.manualTicketClaimDao()
                        .getAllManualSelfTicketClaimData(type)
                        ?: ArrayList()) as ArrayList<ManualTicketClaimImage>
            }

            withContext(Dispatchers.Main) {
                if (imageOfflineModel.size > 0) {
                    txtNoTickets.visibility = View.GONE
                    recyclerViewActivate.layoutManager =
                        LinearLayoutManager(this@ManualTicketDetailsOfflineActivity)
                    if (type == 2) {
                        withContext(Dispatchers.IO) {
                            imageOfflineModel.forEach() {
                                appDatabase.manualTicketClaimDao().updateManualTicketClaimData(
                                    it.id,
                                    it.isUploaded,
                                    it.ticket_number,
                                    System.currentTimeMillis()
                                )
                            }

                        }
                        adapter2 =
                            ManualSelfParticipationAdapter(
                                imageOfflineModel,
                                object : OnClickListener {
                                    override fun onClick(position: Int) {

                                    }
                                },
                                this@ManualTicketDetailsOfflineActivity
                            )
                        recyclerViewActivate.adapter = adapter2
                        adapter2.notifyDataSetChanged()
                    } else {
                        adapter =
                            ManualTicketOfflineAdapter(imageOfflineModel, object : OnClickListener {
                                override fun onClick(position: Int) {}
                            })
                        recyclerViewActivate.adapter = adapter
                        adapter.notifyDataSetChanged()
                    }
                } else {
                    txtNoTickets.visibility = View.VISIBLE
                }
            }
        }

    }

    private fun uploadTicketImage() {
        val offlineData = OfflineDatabase(this@ManualTicketDetailsOfflineActivity)
        offlineData.uploadManualTicketData()
        offlineData.resetManualArrivalDepartureData()
        Handler(Looper.getMainLooper()).postDelayed({
            finish()
        }, 1000)
        makeToast("Your data will be uploaded in background. Please check after sometime.")
    }
}
