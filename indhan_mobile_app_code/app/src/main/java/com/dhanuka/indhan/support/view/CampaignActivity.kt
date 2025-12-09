package com.dhanuka.indhan.support.view

import android.app.Activity
import android.content.Context
import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.recyclerview.widget.GridLayoutManager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.support.RetroInstance
import com.dhanuka.indhan.support.RetrofitService
import com.dhanuka.indhan.support.adapter.CampaignAdapter
import com.dhanuka.indhan.support.model.CampaignModel
import com.dhanuka.indhan.support.model.ManCampaignResponseModel
import kotlinx.android.synthetic.main.activity_support_menu.*
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class CampaignActivity : BaseActivity(), CampaignAdapter.CampaignClickListener {

    lateinit var recyclerViewAdapter: CampaignAdapter
    var list: List<CampaignModel> = listOf()
    var filledCampaigns: List<Int> = listOf()
    private lateinit var preference: Preference
    private lateinit var userEcn: String
    private lateinit var campaignTypeId: String
    private lateinit var campaignTypeName: String
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_support_menu)
        preference = Preference(this)
        campaignTypeId = (intent?.getIntExtra("id", 1) ?: 1).toString()
        campaignTypeName = intent?.getStringExtra("name") ?: ""
        userEcn = preference.getString("ecn")
        iCross.setOnClickListener {
            finish()
        }
        title_support.text = campaignTypeName
        recyclerList.layoutManager =
            GridLayoutManager(this, 1)
        recyclerViewAdapter = CampaignAdapter(this, list)
        showProgress()
        getManCampaigns(this, this)

    }

    override fun onClick(p0: View?) {
    }


    fun getSubMenus(context: Context, click: CampaignAdapter.CampaignClickListener) {
        val retroInstance = RetroInstance.getRetrofitInstance().create(RetrofitService::class.java)
        val call = retroInstance.getCampaigns(userEcn, campaignTypeId.toInt())
        call.enqueue(object : Callback<List<CampaignModel>> {
            override fun onResponse(
                call: Call<List<CampaignModel>>,
                response: Response<List<CampaignModel>>
            ) {
                if (response.body() != null) {
                    val responseBody = response.body()!!
                    recyclerViewAdapter = CampaignAdapter(baseContext, responseBody)
                    recyclerViewAdapter.notifyDataSetChanged()
                    recyclerViewAdapter.setClickListener(click)
                    recyclerList.adapter = recyclerViewAdapter
                } else {
                    Toast.makeText(
                        context,
                        "Server error. Please try again after sometime or with moderate internet connection.",
                        Toast.LENGTH_LONG
                    )
                        .show()
                }
                hideProgress()
            }

            override fun onFailure(call: Call<List<CampaignModel>>, t: Throwable) {
                hideProgress()
                Toast.makeText(context, "Something went wrong! $t", Toast.LENGTH_LONG).show()
            }
        })
    }


    fun getManCampaigns(context: Context, click: CampaignAdapter.CampaignClickListener) {
        val retroInstance = RetroInstance.getRetrofitInstance().create(RetrofitService::class.java)
        val call = retroInstance.getManCampaigns(userEcn)
        call.enqueue(object : Callback<ManCampaignResponseModel> {
            override fun onResponse(
                call: Call<ManCampaignResponseModel>,
                response: Response<ManCampaignResponseModel>
            ) {
                if (response.body() != null) {
                    val responseBody = response.body()!!
                    if (responseBody.status == 200 && responseBody.data?.size!! > 0) {
//                    filledCampaigns = responseBody.data!!
                        for (f in responseBody.data!!) filledCampaigns += f.campId!!
                        getSubMenus(context, click)
                    } else {
                        getSubMenus(context, click)
                    }
                } else {
                    Toast.makeText(
                        context,
                        "Server error. Please try again after sometime or with moderate internet connection.",
                        Toast.LENGTH_LONG
                    )
                        .show()
                }
            }

            override fun onFailure(call: Call<ManCampaignResponseModel>, t: Throwable) {
                print("Mandatory Error: $t")
            }
        })
    }

    fun launchIntent(
        cls: Class<out Activity?>?,
        id: Int,
        isFilled: Boolean,
        campName: String,
        finish: Boolean
    ) {
        val intent = Intent(this, cls)
        intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
        intent.putExtra("id", id)
        intent.putExtra("campName", campName)
        intent.putExtra("isFilled", isFilled)
        startActivity(intent)
        if (finish) finish()
    }

    override fun onClick(view: View, subMenu: CampaignModel) {
        if (filledCampaigns.isNotEmpty()) {
            for (FC in filledCampaigns) {
                if (subMenu.campaign_id == FC) {
                    launchIntent(
                        QaActivity()::class.java,
                        subMenu.campaign_id!!,
                        isFilled = true,
                        campName = subMenu.campaignName.toString(),
                        finish = false
                    )
                } else {
                    launchIntent(
                        QaActivity()::class.java,
                        subMenu.campaign_id!!,
                        isFilled = false,
                        campName = subMenu.campaignName.toString(),
                        finish = false
                    )
                }
            }
        } else {
            launchIntent(
                QaActivity()::class.java,
                subMenu.campaign_id!!,
                isFilled = false,
                campName = subMenu.campaignName.toString(),
                finish = false
            )
        }

    }
}