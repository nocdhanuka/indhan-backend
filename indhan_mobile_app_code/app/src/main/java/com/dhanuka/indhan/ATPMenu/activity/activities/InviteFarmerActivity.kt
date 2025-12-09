package com.dhanuka.indhan.ATPMenu.activity.activities

import android.content.Intent
import android.os.Bundle
import android.os.Handler
import android.os.Looper
import android.text.TextUtils
import android.view.View
import android.widget.Toast
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.ATPMenu.activity.adapters.AddInvitedFarmerAdapter
import com.dhanuka.indhan.ATPMenu.activity.models.CommonAtpResponse
import com.dhanuka.indhan.ATPMenu.activity.models.InviteFarmerList
import com.dhanuka.indhan.ATPMenu.activity.models.ValidatedfarmerListResponse
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.CheckConnectionStatus
import kotlinx.android.synthetic.main.activity_invite_farmer.*
import kotlinx.android.synthetic.main.item_add_invited_farmer.view.*
import org.json.JSONArray
import org.json.JSONObject
import rx.Single
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.text.SimpleDateFormat
import java.util.*

class InviteFarmerActivity : BaseActivity() {
    var fullName = ""
    var contactNo = ""
    var atpPlanId = ""
    private var count = 0
    private lateinit var preference: Preference
    private lateinit var adapter: AddInvitedFarmerAdapter
    private var list = mutableListOf<InviteFarmerList>()

    override fun onClick(v: View?) {
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_invite_farmer)
        preference = Preference(this)

        if (intent.hasExtra(Constants.PLAN_ATP_ID)) {
            atpPlanId = intent?.getStringExtra(Constants.PLAN_ATP_ID) ?: ""
            count = (intent.getStringExtra("KEY") ?: "0").toInt()
        }

        list.add(InviteFarmerList("", ""))
        adapter = AddInvitedFarmerAdapter(this, list, imgAdd)
        val layoutManager =
            LinearLayoutManager(this)
        recyclerViewInvite.layoutManager = layoutManager
        recyclerViewInvite.adapter = adapter

        imgAdd.setOnClickListener {
            if (layoutManager.findFirstCompletelyVisibleItemPosition() == 0) {
                addData()
            } else {
                recyclerViewInvite.smoothScrollToPosition(0)
            }
        }
        applyClickOnView()
    }

    private fun addData() {
        val view: View = recyclerViewInvite.findViewHolderForLayoutPosition(0)!!.itemView
        val contactNo = view.edtContactsNo.text.toString()
        val fullName = view.edtFullName.text.toString()
        if (TextUtils.isEmpty(fullName)) {
            Toast.makeText(this, "Please enter Farmer Name", Toast.LENGTH_SHORT).show()
        } else if (TextUtils.isEmpty(contactNo)) {
            Toast.makeText(this, "Please enter Farmer Contact No.", Toast.LENGTH_SHORT).show()
        } else if (contactNo.length < 10) {
            Toast.makeText(this, "Please Enter Valid Mobile Number", Toast.LENGTH_SHORT).show()
        } else {
            list.add(InviteFarmerList(contactNo, fullName))
            adapter.notifyDataSetChanged()
        }
    }

    private fun addDataSubmit() {
        val view: View? = recyclerViewInvite.findViewHolderForLayoutPosition(0)?.itemView
        val contactNo = (view?.edtContactsNo?.text ?: "").toString()
        val fullName = (view?.edtFullName?.text ?: "").toString()
        if (list.size > 1 && TextUtils.isEmpty(fullName) && TextUtils.isEmpty(contactNo)) {
            inviteFarmerApi()
        } else if (TextUtils.isEmpty(fullName) && TextUtils.isEmpty(contactNo) && list.size == 1) {
            hideProgress()
            makeToast("Please add at least one Farmer ")
        } else if (TextUtils.isEmpty(fullName) && !TextUtils.isEmpty(contactNo)) {
            hideProgress()
            Toast.makeText(this, "Please enter Farmer Name", Toast.LENGTH_SHORT).show()
        } else if (!TextUtils.isEmpty(fullName) && TextUtils.isEmpty(contactNo)) {
            hideProgress()
            Toast.makeText(this, "Please enter Farmer Contact No.", Toast.LENGTH_SHORT).show()
        } else if (!TextUtils.isEmpty(fullName) && !TextUtils.isEmpty(contactNo) && contactNo.length < 10) {
            hideProgress()
            Toast.makeText(this, "Please Enter Valid Mobile Number", Toast.LENGTH_SHORT).show()
        } else if (!TextUtils.isEmpty(fullName) && !TextUtils.isEmpty(contactNo)) {
            list.add(InviteFarmerList(contactNo, fullName))
            if (list.size > 1) {
                inviteFarmerApi()
            }
        }

    }

    //apply click on view
    private fun applyClickOnView() {
        llSubmit.setOnClickListener {
            scrollToPosition(0)
        }
    }

    private fun scrollToPosition(position: Int) {
        if (recyclerViewInvite != null) {
            Handler(Looper.getMainLooper()).post {
                showProgress()
                recyclerViewInvite.scrollToPosition(position)
                if (position == 0) {
                    val layoutManager = recyclerViewInvite.layoutManager as LinearLayoutManager
                    layoutManager.scrollToPositionWithOffset(0, 0)
                    Handler(Looper.getMainLooper()).postDelayed({
                        addDataSubmit()
                    }, 1000)
                }
            }
        }
    }

    //invite farmer api
    private fun inviteFarmerApi() {
        list.toString()
        var jArray = JSONArray()
        if (list.size > 1) {
            for (i in 1 until list.size) {
                list[i]
                var jobj = JSONObject()
                jobj.accumulate("farmer_name", list[i].farmerName)
                jobj.accumulate("contact_number", list[i].contactNumber)
                jArray.put(jobj)
            }
            var model: String = preference.getString("token")
            val query = java.util.HashMap<String, Any>()
            query["apiToken"] = model
            query["plan_atp_id"] = atpPlanId
            query["farmerData"] = jArray.toString()

            if (CheckConnectionStatus.isInternetAvailable(this)) {
                RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
                    .getInviteFarmer(query).subscribeOn(Schedulers.newThread())
                    .observeOn(AndroidSchedulers.mainThread())
                    .subscribe(object : Subscriber<CommonAtpResponse>() {
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

                        override fun onNext(commonBean: CommonAtpResponse) {
                            hideProgress()
                            if (commonBean.status_code == 200) {
                                makeToast(commonBean.message)
                                var i =
                                    Intent(this@InviteFarmerActivity, callingActivity!!::class.java)
                                i.putExtra("COUNT", (count + list.size - 1).toString())
                                setResult(100, i)
                                finish()
                            }
                        }
                    })
            } else {
                addNewFarmerInDb()
            }
        } else {
            makeToast("Please add at least one Farmer ")
        }
    }

    private fun addNewFarmerInDb() {
        dismissProgress()
        Single.fromCallable {
            val formatter1 = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.ENGLISH)
            for (i in list.indices) {
                if (list[i].contactNumber.isNotEmpty() && list[i].farmerName.isNotEmpty()) {
                    val validateFarmer = ValidatedfarmerListResponse.Data(
                        list[i].contactNumber,
                        formatter1.format(Date()),
                        list[i].farmerName,
                        "0",
                        atpPlanId,
                        true
                    )
                    val appDatabase = DatabaseClient.getInstance(this).appDatabase
                    appDatabase.atpFarmerDao().insert(validateFarmer)
                }
            }
            runOnUiThread {
                callingActivity?.let {
                    val i = Intent(this@InviteFarmerActivity, it::class.java)
                    i.putExtra("COUNT", (count + list.size - 1).toString())
                    setResult(100, i)
                    finish()
                }
            }
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }
}
