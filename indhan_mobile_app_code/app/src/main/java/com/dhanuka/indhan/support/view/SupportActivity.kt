package com.dhanuka.indhan.support.view

import android.app.Activity
import android.content.Context
import android.content.Intent
import android.os.Bundle
import androidx.recyclerview.widget.GridLayoutManager
import android.view.View
import android.widget.Toast
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.R
import com.dhanuka.indhan.support.RetroInstance
import com.dhanuka.indhan.support.RetrofitService
import com.dhanuka.indhan.support.adapter.SubMenuAdapter
import com.dhanuka.indhan.support.model.SupportSubMenuBean
import kotlinx.android.synthetic.main.activity_support_menu.*
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class SupportActivity : BaseActivity(), SubMenuAdapter.SubMenuClickListener {

    lateinit var recyclerViewAdapter: SubMenuAdapter
    var list: List<SupportSubMenuBean> = listOf()
    var activity: Activity? = null
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_support_menu)
        recyclerList.layoutManager =
            GridLayoutManager(this, 2)
        recyclerViewAdapter = SubMenuAdapter(this, list, this)
        iCross.setOnClickListener { view ->
            finish()
        }
        showProgress()
        getSubMenus(this, this, this)
    }

    override fun onClick(p0: View?) {
    }


    fun getSubMenus(
        context: Context,
        click: SubMenuAdapter.SubMenuClickListener,
        activity: Activity
    ) {
        val retroInstance = RetroInstance.getRetrofitInstance().create(RetrofitService::class.java)
        val call = retroInstance.getSubMenu()
        call.enqueue(object : Callback<List<SupportSubMenuBean>> {
            override fun onResponse(
                call: Call<List<SupportSubMenuBean>>,
                response: Response<List<SupportSubMenuBean>>
            ) {
                if(response.body() !=null) {
                    val responseBody = response.body()!!
                    recyclerViewAdapter = SubMenuAdapter(baseContext, responseBody, activity)
                    recyclerViewAdapter.notifyDataSetChanged()
                    recyclerViewAdapter.setClickListener(click)
                    recyclerList.adapter = recyclerViewAdapter
                }else{
                    Toast.makeText(context,"Server error. Please try again after sometime or with moderate internet connection.", Toast.LENGTH_LONG)
                        .show()
                }
                hideProgress()
            }

            override fun onFailure(call: Call<List<SupportSubMenuBean>>, t: Throwable) {
            }
        })
    }

    fun launchIntent(cls: Class<out Activity?>?, id: Int, subMenuName: String, finish: Boolean) {
        val intent = Intent(this, cls)
        intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
        intent.putExtra("id", id)
        intent.putExtra("name", subMenuName)
        startActivity(intent)
        if (finish) finish()
    }

    override fun onClick(view: View, subMenu: SupportSubMenuBean) {
        print(subMenu.campaignTypeName)
        launchIntent(
            CampaignActivity::class.java,
            subMenu.campaignTypeId!!,
            subMenu.campaignTypeName.toString(),
            false
        )

    }


}