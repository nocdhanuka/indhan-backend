package com.dhanuka.indhan.RegisterFarmerScreen.offlineFarmerData

import android.content.Intent
import android.os.Bundle
import android.os.Handler
import android.view.View
import androidx.appcompat.app.AlertDialog
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.MenuScreenActivity.OfflineDatabase
import com.dhanuka.indhan.R
import com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration.adapter.IfcFarmerListAdapter
import com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration.model.AddIfcFarmer
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.CheckConnectionStatus
import kotlinx.android.synthetic.main.offline_data_ifc_farmer.*
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.GlobalScope
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class OfflineDataIfcFarmer : BaseActivity(), View.OnClickListener, IfcFarmerListAdapter.OnAction {

    private var arrayFarmer = ArrayList<AddIfcFarmer>()

    private lateinit var dbManager: AppDatabase
    private lateinit var offlineAdapter: IfcFarmerListAdapter
    private var ticketNumber: String = ""

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.ivBack -> {
                onBackPressed()
            }
            R.id.ivDownload -> {
                if (CheckConnectionStatus.isInternetAvailable(this)) {
                    Handler().postDelayed({
                        val resultCode = 101
                        val resultIntent = Intent()
                        setResult(resultCode, resultIntent)
                        finish()
                    }, 800)
                    if (arrayFarmer.size == 0) {
                        makeToast("No Offline Farmer Data Found.")
                    } else {
                        multipleDataUploadFarmer()
                        txtNoTickets.visibility = View.VISIBLE
                        makeToast("Your data will be uploaded in background. Please check after sometime.")
                        arrayFarmer.clear()
                        offlineAdapter.notifyDataSetChanged()
                    }
                } else {
                    makeToast(resources.getString(R.string.no_internet_connection))
                }
            }
        }
    }

    private fun multipleDataUploadFarmer() {
        OfflineDatabase(this@OfflineDataIfcFarmer).hardRefreshIfc()
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.offline_data_ifc_farmer)
        getIntentData()
        ivBack.setOnClickListener(this)
        ivDownload.setOnClickListener(this)
        txtNoTickets.visibility = View.GONE
        dbManager = DatabaseClient.getInstance(this).appDatabase
        getDataFromDataBase()
    }

    private fun getDataFromDataBase() {

        offlineAdapter = IfcFarmerListAdapter(arrayFarmer, this, this@OfflineDataIfcFarmer)
        recyclerViewFarmer.adapter = offlineAdapter

        rx.Observable.fromCallable {
            dbManager.ifcFarmerDao()?.getAllIfcFarmerDataForTicket( ticketNumber,false) as java.util.ArrayList<AddIfcFarmer>
        }.subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread()).subscribe {
                it.distinctBy { it1-> it1.mobile }
                if (it != null) {
                    if (it.size > 0) {
                        arrayFarmer.addAll(it as Collection<AddIfcFarmer>)
                        offlineAdapter.notifyDataSetChanged()
                        if (arrayFarmer.size > 0) {
                            txtNoTickets.visibility = View.GONE
                        }
                    } else {
                        txtNoTickets.visibility = View.VISIBLE
                    }
                } else {
                    txtNoTickets.visibility = View.VISIBLE
                }
            }

        rx.Observable.fromCallable {
            dbManager.ifcFarmerDao()
                ?.getAllIfcFarmerData(2, ticketNumber) as java.util.ArrayList<AddIfcFarmer>
        }.subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread()).subscribe {
                if (it != null) {
                    if (it.size > 0) {
                        arrayFarmer.addAll(
                            it as Collection<AddIfcFarmer>
                        )
                        offlineAdapter.notifyDataSetChanged()
                        if (arrayFarmer.size > 0) {
                            txtNoTickets.visibility = View.GONE
                        }
                    } else {
                        txtNoTickets.visibility = View.VISIBLE
                    }
                } else {
                    txtNoTickets.visibility = View.VISIBLE
                }
            }
    }

    private fun getIntentData() {
        ticketNumber = intent?.getStringExtra("ticket_no") ?: ""
    }

    override fun onDeleteClick(pos: Int, status: Int) {
        AlertDialog.Builder(this).setTitle("Register Farmer")
            .setMessage("Are sure you want to delete farmer.")
            .setPositiveButton(
                "Yes"
            ) { _, _ -> deleteFarmer(pos, status) }.setNegativeButton(
                "No"
            ) { dialogInterface, _ -> dialogInterface.dismiss() }.show()
    }

    private fun deleteFarmer(pos: Int, status: Int) {
        var farmer = java.util.ArrayList<AddIfcFarmer>()
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                dbManager.ifcFarmerDao()?.deleteTableData(arrayFarmer[pos].serverId)
                farmer = (dbManager.ifcFarmerDao()?.getAllIfcFarmerData(0, ticketNumber)
                    ?: java.util.ArrayList<AddIfcFarmer>()) as java.util.ArrayList<AddIfcFarmer>
            }.run {
                withContext(Dispatchers.Main) {
                    if (farmer.isNotEmpty()) {
                        arrayFarmer.clear()
                        arrayFarmer.addAll(farmer)
                        offlineAdapter.notifyDataSetChanged()
                        if (arrayFarmer.size > 0) {
                            txtNoTickets.visibility = View.GONE
                        }
                        withContext(Dispatchers.IO) {
                            dbManager.ifcFarmerDao()?.deleteTableData(arrayFarmer[pos].serverId)
                            farmer = (dbManager.ifcFarmerDao()
                                ?.getAllIfcFarmerData(
                                    2,
                                    ticketNumber
                                )
                                ?: java.util.ArrayList<AddIfcFarmer>()) as java.util.ArrayList<AddIfcFarmer>
                        }.run {
                            withContext(Dispatchers.Main) {
                                arrayFarmer.clear()
                                arrayFarmer.addAll(farmer)
                                offlineAdapter.notifyDataSetChanged()
                                if (arrayFarmer.size > 0) {
                                    txtNoTickets.visibility = View.GONE
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}