generate-proto:
	docker run --rm -v $(PWD):/workspace -w /workspace \
	namely/protoc-all \
	-f protos/wallet.proto \
	-l php \
	-o src/Integrations/WalletService
