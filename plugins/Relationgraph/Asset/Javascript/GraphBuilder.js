$(document).ready(function() {
    var nodesInfo = JSON.parse($('#graph-nodes').html() || null) || null;
    var edgesInfo = JSON.parse($('#graph-edges').html() || null) || null;

    if (nodesInfo) {
	    var nodes = new vis.DataSet(nodesInfo);
	    var edges = new vis.DataSet(edgesInfo);
	
	    var container = document.getElementById('mynetwork');
	    var data = {
	        nodes: nodes,
	        edges: edges
	    };
	
	    var options = {
	        nodes: {borderWidth: 2},
	        interaction: {
	            navigationButtons: true,
	            keyboard: true,
	            hover: true
	        }
	    };
	
	    var network = new vis.Network(container, data, options);
	
	    network.on("selectNode", function (params) {
	        if (params.nodes.length === 1) {
	            var node = nodes.get(params.nodes[0]);
	            window.open(decodeURIComponent(node.url), '_blank');
	        }
	    });
    }
});

