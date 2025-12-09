package com.dhanuka.indhan.institutional.activity.influencers

import android.os.Bundle
import android.text.TextUtils
import android.view.View
import com.bumptech.glide.Glide
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.institutional.activity.TestimonialsListActivity
import com.dhanuka.indhan.institutional.adapter.influencers.InfluencersBranchListAdapter
import com.dhanuka.indhan.institutional.adapter.influencers.InfluencersViewContactAdapter
import com.dhanuka.indhan.institutional.model.influencers.ContactListData
import com.dhanuka.indhan.institutional.model.influencers.InfluencersListData
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.Currency
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_view_influencer_detail.*
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.GlobalScope
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext

class InfluencersDetailActivity : BaseActivity(), InfluencersViewContactAdapter.OnClickItem,
    InfluencersBranchListAdapter.OnClickViewBranchItem {
    private var preference: Preference? = null
    private var appDatabase: AppDatabase? = null
    private var arrayListOfData: ArrayList<ContactListData> = ArrayList()
    private var arrayListOfBranchData: ArrayList<InfluencersListData> = ArrayList()
    private var addContactAdapter: InfluencersViewContactAdapter? = null
    private var branchListAdapter: InfluencersBranchListAdapter? = null
    private var Influencers: InfluencersListData? = null
    private var parentId: String = ""
    private var InfluencersType: String = ""
    private var userType: String = ""

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.btnVisitHistory -> {
                val bundle = Bundle()
                bundle.putString("parentId", parentId)
                bundle.putString("type", InfluencersType)
                bundle.putString("for", "visit_history")
                launchIntent(InfluencersAtpListActivity::class.java, bundle, true)
            }
            R.id.btnViewTestimonial -> {
                val bundle = Bundle()
                bundle.putString("parentId", parentId)
                bundle.putString("type", "3")
                launchIntent(TestimonialsListActivity::class.java, bundle, true)
            }
            R.id.btnAddMoreContact -> {
                val bundle = Bundle()
                bundle.putString("parentId", parentId)
                bundle.putString("type", InfluencersType)
                launchIntent(AddMoreContactInfluencersActivity::class.java, bundle, true)
            }
            R.id.btnAddNewBranch -> {
                val bundle = Bundle()
                bundle.putString("parentId", parentId)
                bundle.putString("type", InfluencersType)
                bundle.putString("branch", "branch")
                launchIntent(AddInfluencersBranchActivity::class.java, bundle, true)
            }
            R.id.ivBack -> {
                finish()
            }
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_view_influencer_detail)
        preference = Preference(this)
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        userType = preference?.getString("user_type") ?: ""
        intent?.getStringExtra("influencers_data")?.let {
            Influencers = Gson().fromJson(it, InfluencersListData::class.java)
            parentId = (Influencers?.influencer_id ?: "").toString()
            InfluencersType = (Influencers?.influencer_type ?: "").toString()
            Influencers?.contact_data?.let { contactList ->
                contactList.forEach() { contact ->
                    arrayListOfData.add(contact)
                }
            }

            GlobalScope.launch {
                withContext(Dispatchers.IO) {
                    arrayListOfBranchData = (appDatabase?.influencersListDao()
                        ?.allInfluencersBranchByOfficeTypeAndInfluencersType(
                            Influencers?.influencer_id ?: 0, "2",
                        ) ?: ArrayList<InfluencersListData>()) as ArrayList<InfluencersListData>
                }.run {
                    withContext(Dispatchers.Main) {
                        setAdapter()
                        setData()
                    }
                }
            }
        }

        intent?.getStringExtra("from_tea").let {
            if (it != null) {
                btnAddNewBranch.visibility = View.VISIBLE
                relBranchDetail.visibility = View.VISIBLE
            } else {
                relBranchDetail.visibility = View.GONE
                btnAddNewBranch.visibility = View.GONE
            }
        }
        btnVisitHistory.setOnClickListener(this)
        btnAddNewBranch.setOnClickListener(this)
        btnAddMoreContact.setOnClickListener(this)
        ivBack.setOnClickListener(this)
        btnViewTestimonial.setOnClickListener(this)
    }

    private fun setData() {
        Glide.with(this)
            .asBitmap()
            .load(Constants.IMAGEBASEURL + Influencers?.image)
            .into(ivCompanyImage)

        officeType.text =
            "Influencer - ".plus(if (Influencers?.office_type == "1") "Head Office" else "Branch Office")
        tvManagerName.text = "Assigned To - ".plus(Influencers?.fl_name)
        tvFlName.visibility = View.GONE
        llType.visibility = View.GONE
        llTurnOverWords.visibility = View.GONE
        llDistrict.visibility = View.GONE
        tvCompanyName.text = Influencers?.name
        llCategory.visibility = View.GONE
        tvTurnover.text = Influencers?.turn_over
        if (!TextUtils.isEmpty(Influencers?.district)) {
            tvDistrict.visibility = View.VISIBLE
            tvDistrict.text = Influencers?.district ?: ""
        } else {
            tvDistrict.visibility = View.GONE
            tvDistrict.text = "-"
        }
        if (!TextUtils.isEmpty(Influencers?.state)) {
            tvState.text = Influencers?.state ?: ""
        } else {
            tvState.text = "-"
        }

        if (!TextUtils.isEmpty(Influencers?.turn_over)) {
            "In Words - ${
                Currency.convertToIndianCurrency(
                    Influencers?.turn_over.toString().trim()
                )
            }".also { tvInWords.text = it }
        }
        tvLandArea.text = Influencers?.land_area
        tvCompanyType.text = getCompanyType(Influencers?.influencer_company_id)
        tvAddress.text = Influencers?.address
    }

    private fun getCompanyType(sugarmillCompanyId: Int?): CharSequence {
        return when (sugarmillCompanyId) {
            1 -> {
                "KVK"
            }
            2 -> {
                "ICAR"
            }
            3 -> {
                "DKM"
            }
            else -> {
                ""
            }
        }
    }

    private fun setAdapter() {
        //Contact Adapter
        addContactAdapter =
            InfluencersViewContactAdapter(this@InfluencersDetailActivity, arrayListOfData)
        rcvContact.adapter = addContactAdapter
        addContactAdapter?.setClickListener(this)

        //Branch Adapter
        branchListAdapter =
            InfluencersBranchListAdapter(this@InfluencersDetailActivity, arrayListOfBranchData)
        rcvBranch.adapter = branchListAdapter
        branchListAdapter?.setViewClickListener(this)
    }

    override fun onClickView(view: View, subMenu: ContactListData) {
        //contact
    }

    override fun onClickView(view: View, subMenu: InfluencersListData) {
        //branch
        val bundle = Bundle()
        bundle.putString("influencers_data", Gson().toJson(subMenu))
        launchIntent(InfluencersDetailActivity::class.java, bundle, true)
    }
}