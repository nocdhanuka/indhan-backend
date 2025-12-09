package com.dhanuka.indhan.ATPMenu.activity.supervisorAtp

import android.content.Intent
import android.os.Bundle
import android.view.View
import androidx.fragment.app.Fragment
import androidx.localbroadcastmanager.content.LocalBroadcastManager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DataBase.DataBaseHelperNew
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.activity_demo_request_evaluated.tabLayout
import kotlinx.android.synthetic.main.activity_demo_request_evaluated.viewPagerRerquest
import kotlinx.android.synthetic.main.activity_supervisor_atp.*


class SupervisorATPActivity : BaseActivity() {

    private var productId = ""
    private var cropId = ""
    private var fromDate = ""
    private var endDate = ""
    private var rsmId = ""
    private var asmId = ""
    private var flId = ""
    private var ddId = ""
    override fun onClick(v: View?) {

    }

    private lateinit var dataBaseHelperNew: DataBaseHelperNew

    private lateinit var adapter: SupervisorATPAdapter
    private lateinit var preference: Preference


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_supervisor_atp)
        preference = Preference(this)
        dataBaseHelperNew = DataBaseHelperNew(this)

        setUpTabs()

        imgFilter.setOnClickListener {
            val i = Intent(this@SupervisorATPActivity, SupervisorATPFilterActivity::class.java)
            startActivityForResult(i, 100)
        }
    }


    /**
     * setup tabs for
     * */
    private fun setUpTabs() {

        adapter =
            SupervisorATPAdapter(this@SupervisorATPActivity, supportFragmentManager, tabLayout)
        viewPagerRerquest.adapter = adapter
        viewPagerRerquest.offscreenPageLimit = 4
        tabLayout.setupWithViewPager(viewPagerRerquest)


    }


    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {


        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == 100 && resultCode == 120) {
            data?.let {
                cropId = it.getStringExtra(Constants.CROP_ID) ?: ""
                productId = it.getStringExtra(Constants.PRODUCT_ID) ?: ""
                fromDate = it.getStringExtra(Constants.FROM_DATE) ?: ""
                endDate = it.getStringExtra(Constants.TO_DATE) ?: ""
                ddId = it.getStringExtra(Constants.PRODUCT_NAME) ?: ""
                rsmId = it.getStringExtra("RSMID") ?: ""
                asmId = it.getStringExtra("ASMID") ?: ""
                flId = it.getStringExtra("FLID") ?: ""
            }
            val i = Intent("custom-event-name")
            i.putExtra(Constants.PRODUCT_NAME, ddId)
            i.putExtra(Constants.CROP_NAME, cropId)
            i.putExtra(Constants.PRODUCT_ID, productId)
            i.putExtra(Constants.CROP_ID, cropId)
            i.putExtra(Constants.FROM_DATE, fromDate)
            i.putExtra(Constants.TO_DATE, endDate)
            i.putExtra(Constants.FROM, "filter")
            i.putExtra("FLID", flId)
            i.putExtra("RSMID", rsmId)
            i.putExtra("ASMID", asmId)
            LocalBroadcastManager.getInstance(this).sendBroadcast(i)

        }
        var fragment: Fragment? = null
        when (val index: Int = viewPagerRerquest.currentItem) {

            0 -> {
                fragment = adapter.fragments[index]
                fragment?.onActivityResult(requestCode, resultCode, data)
            }
            1 -> {
                fragment = adapter.fragments[index]
                fragment?.onActivityResult(requestCode, resultCode, data)
            }
            2 -> {
                fragment = adapter.fragments[index]
                fragment?.onActivityResult(requestCode, resultCode, data)
            }
            3 -> {
                fragment = adapter.fragments[index]
                fragment?.onActivityResult(requestCode, resultCode, data)
            }

        }
    }


    override fun onDestroy() {
        preference.putString(Constants.PRODUCT_NAME, "")
        preference.putString(Constants.CROP_NAME, "")
        preference.putString(Constants.PRODUCT_ID, "")
        preference.putString(Constants.CROP_ID, "")
        preference.putString(Constants.FROM_DATE, "")
        preference.putString(Constants.TO_DATE, "")
        preference.putString("FLID", "")
        preference.putString("RSMID", "")
        preference.putString("ASMID", "")
        super.onDestroy()
    }


}
