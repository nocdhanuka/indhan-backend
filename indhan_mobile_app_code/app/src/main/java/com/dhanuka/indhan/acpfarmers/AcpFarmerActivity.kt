package com.dhanuka.indhan.acpfarmers

import android.app.Activity
import android.content.Intent
import android.os.Bundle
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import kotlinx.android.synthetic.main.activity_acp_farmer.*
import org.greenrobot.eventbus.EventBus
import org.joda.time.DateTime
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.io.Serializable
import java.text.SimpleDateFormat
import java.util.*

class AcpFarmerActivity : BaseActivity() {
    override fun onClick(v: View?) {

    }

    private lateinit var preference: Preference
    private lateinit var adapter: ACPFarmerAdapter
    private lateinit var data: ACPFarmerBean
    private var farmerList = mutableListOf<ACPFarmerBean.Data.ACPDetail>()
    private var ddList = mutableListOf<ACPFarmerBean.Data.DDList>()
    var cropId = ""
    var acr = ""
    var ddId = ""
    var villageId = ""
    var fromDate = ""
    var toDate = ""
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_acp_farmer)
        preference = Preference(this)
        if (!preference.getString("user_type").contains("bdo", ignoreCase = true)
            && !preference.getString("user_type").contains("doctor", ignoreCase = true)
        ) {
            val intent = Intent(activity, AcpFarmerFilterActivity::class.java)
            intent.putExtra("ddlist", ddList as Serializable)
            startActivityForResult(intent, 100)
        } else {
            getACPList()
        }


        imgMAp.setOnClickListener {

            val intent = Intent(activity, AcpFarmerMapList::class.java)
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            startActivity(intent)


        }
        imgFilter.setOnClickListener {
            val intent = Intent(activity, AcpFarmerFilterActivity::class.java)
            intent.putExtra("ddlist", ddList as Serializable)
            startActivityForResult(intent, 100)
        }


    }

    /*
    * get DD list
    * */
    private fun getACPList() {
        showProgress()
        val c = Calendar.getInstance().time
        val df = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
        val formattedDate: String = df.format(c)

        val c1 = Calendar.getInstance()
        c1.time = DateTime.now().toDate()
        c1.add(Calendar.DATE, -30)
        val nextDate = c1.time
        val formattedFromDate: String = df.format(nextDate)
        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
        query["dd_id"] = ddId
        query["updated_at"] = "0"
        query["village"] = villageId

        query["from"] = fromDate
        query["to"] = toDate
        query["crop"] = cropId
        query["acerage"] = acr

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getBDOFarmerList(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ACPFarmerBean>() {
                override fun onCompleted() {
                    hideProgress()
                }


                override fun onError(e: Throwable) {
                    hideProgress()

                }

                override fun onNext(listBean: ACPFarmerBean) {
                    hideProgress()

                    if (listBean.statusCode == 200) {
                        data = listBean
                        farmerList.clear()
                        farmerList.addAll(listBean.data.aCPDetail)

                        adapter = ACPFarmerAdapter(this@AcpFarmerActivity, farmerList)
                        recyclerViewACPFarmer.layoutManager =
                            LinearLayoutManager(this@AcpFarmerActivity)
                        recyclerViewACPFarmer.adapter = adapter
                        if (farmerList.size > 0) {
                            txtEmpty.visibility = View.GONE
                        } else {
                            txtEmpty.visibility = View.VISIBLE
                        }
                        txtTotalACP.text = listBean.data.totalACPFamer.toString()
                        txtTotalAcrerageValue.text = listBean.data.totalAcres.toString()
                        txtTotalLeadValue.text = listBean.data.totalLeads.toString()
                        txtTotalSoldValue.text = listBean.data.totalClosedAcres.toString()
                        //dd list
                        ddList.clear()
                        ddList.addAll(listBean.data.dDList)
                    } else {
                        if (listBean.statusCode == 409) {
                            EventBus.getDefault().post("logout")
                        }
                    }
                }
            })

    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == 100) {
            if (resultCode == Activity.RESULT_OK) {
                data?.let {
                    if (it.hasExtra("cropid")) {
                        cropId = it.getStringExtra("cropid") ?: ""
                    }
                    if (it.hasExtra("acr")) {
                        acr = it.getStringExtra("acr") ?: ""
                    }
                    if (it.hasExtra("ddid")) {
                        ddId = it.getStringExtra("ddid") ?: ""
                    }
                    if (it.hasExtra("villageid")) {
                        villageId = it.getStringExtra("villageid") ?: ""
                    }
                    if (it.hasExtra("fromdate")) {
                        fromDate = it.getStringExtra("fromdate") ?: ""
                    }
                    if (it.hasExtra("todate")) {
                        toDate = it.getStringExtra("todate") ?: ""
                    }
                    //for filter
                    getACPList()
                }
            }
        }
    }
}
