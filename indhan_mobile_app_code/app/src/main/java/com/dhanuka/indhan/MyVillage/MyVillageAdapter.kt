package com.dhanuka.indhan.MyVillage

import android.content.Context
import android.content.Intent
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.ViewGroup
import com.dhanuka.indhan.R
import com.dhanuka.indhan.VillageDetails.VillageDetailsActivity
import com.dhanuka.indhan.databinding.ItemMyVillageBinding
import com.google.gson.Gson


class MyVillageAdapter(var context: Context, var list: MutableList<MyVillageBean.MyVillageData>) :
    RecyclerView.Adapter<MyVillageAdapter.ViewHolder>() {

    var layoutInflater = LayoutInflater.from(context)
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding: ItemMyVillageBinding =
            DataBindingUtil.inflate(layoutInflater, R.layout.item_my_village, parent, false)
        return ViewHolder(binding)
    }

    override fun getItemCount(): Int {
        return list.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {

        holder.setData(position)


    }


    inner class ViewHolder(var view: ItemMyVillageBinding) : RecyclerView.ViewHolder(view.root) {
        // Holds the TextView that will add each animal to
        fun setData(position: Int) {

            view.txtVillageName.text = list[position].villageName
            view.txtNoOfFarmers.text = "${list[position].farmers} Farmers"
            view.txtAvgVisit.text = "${list[position].avgVisit} visit/month/farmer"

            view.llayout.setOnClickListener {
                val intent = Intent(context, VillageDetailsActivity::class.java)
                intent.putExtra("data", Gson().toJson(list[position]))
                intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
                context.startActivity(intent)
            }

        }


    }
}