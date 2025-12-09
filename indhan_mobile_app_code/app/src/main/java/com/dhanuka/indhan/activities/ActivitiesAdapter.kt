package com.dhanuka.indhan.activities

import android.content.Context
import android.content.Intent
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.ViewGroup
import com.dhanuka.indhan.Activity.TicketDetailActivity
import com.dhanuka.indhan.Activity.TicketHistoryMVP.Model.TicketHistoryModel
import com.dhanuka.indhan.R
import com.dhanuka.indhan.databinding.ItemActivitiesBinding
import com.google.gson.Gson


class ActivitiesAdapter(var context: Context, var list: MutableList<ActivitiesBean.Data.Activity>) :
    RecyclerView.Adapter<ActivitiesAdapter.ViewHolder>() {

    var layoutInflater = LayoutInflater.from(context)
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding: ItemActivitiesBinding =
            DataBindingUtil.inflate(layoutInflater, R.layout.item_activities, parent, false)
        return ViewHolder(binding)
    }

    override fun getItemCount(): Int {
        return list.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {

        holder.setData(position)


    }


    inner class ViewHolder(var view: ItemActivitiesBinding) : RecyclerView.ViewHolder(view.root) {
        // Holds the TextView that will add each animal to
        fun setData(position: Int) {
            view.txtActivitiesID.text = list[position].tID
            view.txtCropName.text = list[position].crop1
            view.txtStartDate.text = list[position].datetime
            view.txtEndDate.text = list[position].close_date
            view.txtParticipationPercentage.text = list[position].validatedFarmers.toString()

            var json = Gson().toJson(list[position])
            view.llayout.setOnClickListener {
                var i = Intent(context, TicketDetailActivity::class.java)
                i.putExtra("ticket", Gson().fromJson(json, TicketHistoryModel.Datum::class.java))
                context.startActivity(i)
            }

        }


    }


}