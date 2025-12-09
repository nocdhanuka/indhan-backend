package com.dhanuka.indhan.Activity.DataCollection.distributor

import android.os.Build
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.Model.AllDistDistrictReponse
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.item_distributor_district_data.view.*
import kotlin.math.roundToInt

class DistrictWiseItemAdapter(
    itemList: ArrayList<AllDistDistrictReponse.Data>,
    distrcitDashboard: DistributorDashBoard
) :
    RecyclerView.Adapter<DistrictWiseItemAdapter.MyViewHolder>() {
    private var itemListData = ArrayList<AllDistDistrictReponse.Data>()
    var listener: OnDistrictSelected

    init {
        this.itemListData = itemList
        this.listener = distrcitDashboard
    }

    override fun onCreateViewHolder(parent: ViewGroup, view: Int): MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_distributor_district_data, parent, false)
        return MyViewHolder(itemView)
    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        var itemViews: View = view

        fun bindItems(
            districtData: AllDistDistrictReponse.Data,
            position: Int,
            listener: OnDistrictSelected
        ) {
            itemViews.llRoot.setOnClickListener {
                listener.onItemClick(position)
            }
            itemViews.tvDistrictName.text = districtData.district_name
            itemViews.tvAddedCountName.text = districtData.distributor_by_ecn.toString()
            itemViews.txtLeftValue.text = districtData.rating_distributor.toString()
            itemViews.txtRightValue.text = districtData.nonrating_distributor.toString()
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
                if (districtData.rating_distributor != 0 && districtData.admin_distributor != 0) {
                    itemViews.pgrIndicatorBar.setProgress(
                        (((districtData.rating_distributor.toDouble()
                            .div(districtData.admin_distributor.toDouble())) * 100).roundToInt()),
                        true
                    )
                }
            } else {
                if (districtData.rating_distributor != 0 && districtData.admin_distributor != 0) {
                    itemViews.pgrIndicatorBar.progress =
                        ((districtData.rating_distributor.toDouble()
                            .div(districtData.admin_distributor.toDouble())) * 100).roundToInt()
                }
            }
        }
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        holder.bindItems(itemListData[position], position, listener)
    }

    override fun getItemCount(): Int = itemListData.size

    interface OnDistrictSelected {
        fun onItemClick(position: Int)
    }
}