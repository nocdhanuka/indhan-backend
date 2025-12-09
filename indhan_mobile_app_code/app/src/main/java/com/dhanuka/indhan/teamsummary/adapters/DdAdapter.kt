package com.dhanuka.indhan.teamsummary.adapters

import android.content.Context
import android.content.Intent
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.MyLeadMenu.activities.myleadactivity.MyLeadActivity
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.myteam.MyTeamDetailsActivity
import com.dhanuka.indhan.teamsummary.models.TeamSummaryDdDetail
import kotlinx.android.synthetic.main.item_dd.view.*
import java.io.Serializable

class DdAdapter(context: Context, ddList: ArrayList<TeamSummaryDdDetail>) :
    RecyclerView.Adapter<DdAdapter.ViewHolder>() {
    var context: Context
    var ddList: ArrayList<TeamSummaryDdDetail>
    lateinit var preference: Preference


    init {
        this.context = context
        this.ddList = ddList
        preference = Preference(context)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view: View = LayoutInflater.from(context).inflate(R.layout.item_dd, parent, false)
        return ViewHolder(view)
    }

    override fun getItemCount(): Int {
        return ddList.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.view.txtDdName.text = ddList[position].name

        holder.view.llMain.setOnClickListener {
            if (preference.getString("user_type").equals("BDO", false)) {
                var intent = Intent(context, MyLeadActivity::class.java)
                intent.putExtra("ddID", ddList[position].dd_id)
                intent.putExtra(Constants.DDLIST, ddList as Serializable)
                context.startActivity(intent)
            } else if (!preference.getString("user_type").equals("BDO", false)) {
                var intent = Intent(context, MyTeamDetailsActivity::class.java)
                intent.putExtra("ecn", ddList[position].ECN)
                context.startActivity(intent)


            }

        }
    }

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        var view: View

        init {
            this.view = view
        }
    }
}