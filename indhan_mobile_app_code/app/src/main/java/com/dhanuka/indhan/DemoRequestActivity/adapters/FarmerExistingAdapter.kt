package com.dhanuka.indhan.DemoRequestActivity.adapters

import android.content.Context
import android.content.Intent
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.bumptech.glide.Glide
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DemoRequestActivity.activity.CreateRequestActivity.CreateRequestActivity
import com.dhanuka.indhan.DemoRequestActivity.models.ExistingData
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.OnSuccess
import kotlinx.android.synthetic.main.item_existing_farmer.view.*
import java.io.Serializable
import java.util.*

class FarmerExistingAdapter(context: Context, demoList: ArrayList<ExistingData>, param: OnSuccess) :
    RecyclerView.Adapter<FarmerExistingAdapter.ViewHolder>() {
    var context: Context
    var demoList: ArrayList<ExistingData>
    var listener: OnSuccess

    init {
        this.context = context
        this.demoList = demoList
        this.listener = param
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view: View =
            LayoutInflater.from(context).inflate(R.layout.item_existing_farmer, parent, false)
        return ViewHolder(view)
    }

    override fun getItemCount(): Int {
        return demoList.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.view.txtFarmerNames.text = demoList.get(position).farmerName
        if (demoList.get(position).farmarImage != null && !demoList.get(position).farmarImage.equals(
                ""
            )
        ) {
            Glide.with(context)
                .load(demoList[position].farmarImage)
                .into(holder.view.imgFarmer)
        }


        holder.view.chbSelectFarmer.setOnCheckedChangeListener { buttonView, isChecked ->

            if (isChecked) {
                val getFarmerListIntent = Intent(context, CreateRequestActivity::class.java)
                getFarmerListIntent.putExtra(
                    Constants.FARMER_LIST,
                    demoList.get(position) as Serializable
                )
                getFarmerListIntent.putExtra(Constants.FROM_EXISTING, "yes")
                context.startActivity(getFarmerListIntent)
                listener.onClick("back")

            }
        }


        /*holder.view.llProductDetail.setOnClickListener {
            var showDetailIntent = Intent(context, RequestsDetailActivity::class.java)
            showDetailIntent.putExtra(Constants.SEND_DEMO_REQUEST_DATA, demoList.get(position) as Serializable)
            context.startActivity(showDetailIntent)
        }*/


    }

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        var view: View

        init {
            this.view = view
        }
    }
}