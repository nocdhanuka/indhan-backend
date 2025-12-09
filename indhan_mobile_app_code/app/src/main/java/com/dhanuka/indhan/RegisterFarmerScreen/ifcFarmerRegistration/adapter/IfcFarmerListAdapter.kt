package com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration.adapter

import android.app.Activity
import android.text.TextUtils
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.bumptech.glide.request.RequestOptions
import com.dhanuka.indhan.R
import com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration.model.AddIfcFarmer
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.list_ifc_farmer_row.view.*
import java.io.File


class IfcFarmerListAdapter(
    private val filteredData: MutableList<AddIfcFarmer>?,
    private val mActivityContext: Activity,
    ifcFarmerRegistrationActivity: Activity
) : RecyclerView.Adapter<IfcFarmerListAdapter.MyViewHolder>() {

    private var action: OnAction

    init {
        ifcFarmerRegistrationActivity.also { action = it as OnAction }
    }

    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewType: Int
    ): IfcFarmerListAdapter.MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.list_ifc_farmer_row, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: IfcFarmerListAdapter.MyViewHolder, p0: Int) {
        val position = holder.absoluteAdapterPosition
        holder.itemView.tvFarmerName.text = filteredData!![position].farmer_name
        holder.itemView.tvFarmerMobile.text = filteredData[position].mobile
        holder.itemView.tvFarmerAcerage.text =
            Utility.roundOffDecimal(filteredData[position].acrers.toDouble()).toString()

        holder.itemView.ivDelete.setOnClickListener {
            action.onDeleteClick(position, filteredData[position].status)
        }

        if (filteredData[position].status == 0) {
            holder.itemView.offlineTicket.visibility = View.VISIBLE
        } else {
            holder.itemView.offlineTicket.visibility = View.GONE
        }

        if (!TextUtils.isEmpty(filteredData[position].image)) {
            val requestOptions = RequestOptions()
            requestOptions.placeholder(R.drawable.name)
            requestOptions.error(R.drawable.name)
            if (filteredData[position].image!!.contains("https://")) {
                Glide.with(mActivityContext)
                    .setDefaultRequestOptions(requestOptions)
                    .load(filteredData[position].image)
                    .into(holder.itemView.ivFarmerImage)
            } else {
                Glide.with(mActivityContext)
                    .setDefaultRequestOptions(requestOptions)
                    .load(File(filteredData[position].image))
                    .into(holder.itemView.ivFarmerImage)
            }
        }

    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {

    }

    public interface OnAction {
        fun onDeleteClick(pos: Int, status: Int)
    }

    override fun getItemCount(): Int {
        return filteredData!!.size
    }
}